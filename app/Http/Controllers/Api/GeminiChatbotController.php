<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Product, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiChatbotController extends Controller
{
    private const MODEL = 'gemini-2.5-flash';

    private const SYSTEM = <<<PROMPT
Bạn là trợ lý AI của NovaPhone - cửa hàng điện thoại. Nhiệm vụ:
- Tư vấn sản phẩm theo nhu cầu và ngân sách của khách.
- Phân tích doanh thu, sản phẩm bán chạy khi admin hỏi.
-Thu thập thông tin của khách hàng ,phân tích su hướng mua hàng của mỗi khách hàng
- Trả lời bằng tiếng Việt, ngắn gọn, thân thiện.
Luôn dùng function để lấy dữ liệu thật, không tự bịa giá hay tồn kho.
Khi giới thiệu sản phẩm, với MỖI sản phẩm hãy trình bày theo mẫu:
![tên sản phẩm](thumbnail)
[tên sản phẩm](url) - giá bán
Trong đó thumbnail và url lấy nguyên văn từ kết quả function, tuyệt đối không tự bịa link. Nếu thumbnail là null thì bỏ qua dòng ảnh.
Giá luôn hiển thị định dạng Việt Nam, ví dụ 6990000.00 viết thành 6.990.000đ. Nếu có sale_price thì hiển thị giá sale kèm giá gốc gạch ngang không cần, chỉ ghi "giảm còn ...".
PROMPT;

    public function chat(Request $request)
    {
        $request->validate(['messages' => 'required|array']);

        // Chuyển format frontend {role: user|assistant} -> Gemini {role: user|model}
        $contents = collect($request->input('messages'))->map(fn($m) => [
            'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ])->values()->all();

        $response = $this->callGemini($contents);

        // Vòng lặp function calling: chạy tool tới khi model trả text
        for ($i = 0; $i < 5; $i++) {
            $parts = $response['candidates'][0]['content']['parts'] ?? [];
            $calls = array_filter($parts, fn($p) => isset($p['functionCall']));
            if (empty($calls)) break;

            // Thêm lượt của model (chứa functionCall) vào lịch sử
            $contents[] = ['role' => 'model', 'parts' => $parts];

            // Thực thi từng function và trả kết quả
            $responseParts = [];
            foreach ($calls as $call) {
                $fn = $call['functionCall'];
                $responseParts[] = [
                    'functionResponse' => [
                        'name'     => $fn['name'],
                        'response' => ['result' => $this->executeTool($fn['name'], $fn['args'] ?? [])],
                    ],
                ];
            }
            $contents[] = ['role' => 'user', 'parts' => $responseParts];

            $response = $this->callGemini($contents);
        }

        $reply = collect($response['candidates'][0]['content']['parts'] ?? [])
            ->pluck('text')->filter()->implode('');

        return response()->json(['reply' => $reply ?: 'Xin lỗi, mình chưa xử lý được yêu cầu này.']);
    }

    private function callGemini(array $contents): array
    {
        $res = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->withOptions(['verify' => storage_path('certs/cacert.pem')])
            ->timeout(60)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . self::MODEL . ':generateContent', [
                'systemInstruction' => ['parts' => [['text' => self::SYSTEM]]],
                'contents' => $contents,
                'tools' => [['functionDeclarations' => $this->functionDeclarations()]],
            ]);

        $res->throw();
        return $res->json();
    }

    private function functionDeclarations(): array
    {
        return [
            [
                'name' => 'search_products',
                'description' => 'Tìm sản phẩm theo tên/hãng và khoảng giá. Gọi khi khách hỏi tư vấn hoặc so sánh điện thoại.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword'   => ['type' => 'string'],
                        'min_price' => ['type' => 'number'],
                        'max_price' => ['type' => 'number'],
                    ],
                ],
            ],
            [
                'name' => 'get_revenue_stats',
                'description' => 'Thống kê doanh thu và số đơn theo khoảng ngày (YYYY-MM-DD).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'from' => ['type' => 'string'],
                        'to'   => ['type' => 'string'],
                    ],
                    'required' => ['from', 'to'],
                ],
            ],
            [
                'name' => 'top_selling_products',
                'description' => 'Danh sách sản phẩm bán chạy nhất.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => ['limit' => ['type' => 'integer']],
                ],
            ],
        ];
    }

    private function executeTool(string $name, array $input): string
    {
        return match ($name) {
            'search_products' => Product::query()
                ->where('is_active', true)
                ->when($input['keyword'] ?? null, fn($q, $k) => $q->where(fn($qq) =>
                    $qq->where('name', 'like', "%$k%")
                       ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%$k%"))))
                ->when($input['min_price'] ?? null, fn($q, $p) => $q->where('price', '>=', $p))
                ->when($input['max_price'] ?? null, fn($q, $p) => $q->where('price', '<=', $p))
                ->orderByDesc('sold_count')
                ->limit(5)->get(['id', 'name', 'slug', 'price', 'sale_price', 'sold_count', 'thumbnail'])
                ->map(fn($p) => [
                    'name'       => $p->name,
                    'price'      => $p->price,
                    'sale_price' => $p->sale_price,
                    'sold_count' => $p->sold_count,
                    'url'        => url('/products/' . $p->slug),
                    'thumbnail'  => $p->thumbnail,
                ])->toJson(JSON_UNESCAPED_UNICODE),

            'get_revenue_stats' => Order::whereBetween('created_at', [$input['from'], $input['to']])
                ->where('status', 'delivered')
                ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as revenue')
                ->first()->toJson(),

            'top_selling_products' => OrderItem::selectRaw('product_id, MAX(product_name) as product_name, SUM(quantity) as sold')
                ->groupBy('product_id')->orderByDesc('sold')
                ->limit($input['limit'] ?? 5)
                ->with('product:id,slug,thumbnail,price')
                ->get()->map(fn($i) => [
                    'product_name' => $i->product_name,
                    'sold'         => (int) $i->sold,
                    'price'        => $i->product?->price,
                    'url'          => $i->product ? url('/products/' . $i->product->slug) : null,
                    'thumbnail'    => $i->product?->thumbnail,
                ])->toJson(JSON_UNESCAPED_UNICODE),

            default => json_encode(['error' => "Unknown tool: $name"]),
        };
    }
}
