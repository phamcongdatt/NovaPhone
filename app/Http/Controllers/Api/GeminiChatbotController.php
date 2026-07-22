<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class GeminiChatbotController extends Controller
{
    private const MODEL = 'gemini-2.5-flash';

    private const SYSTEM = <<<PROMPT
Ban la tro ly AI cua NovaPhone.

Nhiem vu:
- Tu van dien thoai
- Goi y phu kien
- So sanh san pham
- Giai thich thong so
- Huong dan mua hang

Khong duoc tra loi bat ky thong tin noi bo nao nhu:
- doanh thu
- loi nhuan
- ton kho
- so luong don
- khach hang
- du lieu nhan vien

Neu nguoi dung hoi cac thong tin tren hay lich su tu choi.
Ban phai tra loi bang tieng Viet, ngan gon va than thien.
Luon dung function de lay du lieu that, khong tu bia gia hay ton kho.
Khi gioi thieu san pham, voi moi san pham hay trinh bay theo mau:
![ten san pham](thumbnail)
[ten san pham](url) - gia ban
Neu thumbnail la null thi bo qua dong anh.
Gia luon hien thi dinh dang Viet Nam, vi du 6990000.00 viet thanh 6.990.000d.
Neu co sale_price thi hien thi gia sale kem gia goc gach ngang khong can, chi ghi "giam con ...".
Neu khach mac ca:
- giai thich gia
- gioi thieu san pham re hon
- gioi thieu may dang giam gia
PROMPT;

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'messages' => ['required', 'array'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string'],
        ]);

        try {
            $contents = collect($validated['messages'])->map(static function (array $message): array {
                return [
                    'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $message['content']]],
                ];
            })->values()->all();

            $response = $this->callGemini($contents);

            for ($i = 0; $i < 5; $i++) {
                $parts = data_get($response, 'candidates.0.content.parts', []);
                $calls = array_values(array_filter($parts, static fn ($part) => isset($part['functionCall'])));

                if ($calls === []) {
                    break;
                }

                $contents[] = ['role' => 'model', 'parts' => $parts];

                $responseParts = [];
                foreach ($calls as $call) {
                    $fn = $call['functionCall'];
                    $responseParts[] = [
                        'functionResponse' => [
                            'name' => $fn['name'],
                            'response' => [
                                'result' => $this->executeTool($fn['name'], $fn['args'] ?? []),
                            ],
                        ],
                    ];
                }

                $contents[] = ['role' => 'user', 'parts' => $responseParts];
                $response = $this->callGemini($contents);
            }

            $reply = collect(data_get($response, 'candidates.0.content.parts', []))
                ->pluck('text')
                ->filter()
                ->implode('');

            return response()->json([
                'reply' => $reply ?: 'Xin loi, minh chua xu ly duoc yeu cau nay.',
            ]);
        } catch (RequestException $e) {
            report($e);

            if ($e->response?->status() === 429) {
                return response()->json([
                    'reply' => 'Tam thoi Gemini dang het quota hoac vuot gioi han. Ban thu lai sau it phut nhe.',
                ], 503);
            }

            return response()->json([
                'reply' => 'Xin loi, chatbot dang gap loi khi goi Gemini. Ban thu lai sau nhe.',
            ], 502);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'reply' => 'Xin loi, chatbot dang gap loi noi bo. Ban thu lai sau nhe.',
            ], 500);
        }
    }

    private function callGemini(array $contents): array
    {
        $apiKey = trim((string) config('services.gemini.key'));

        if ($apiKey === '') {
            abort(500, 'GEMINI_API_KEY is not configured.');
        }

        $res = Http::withHeaders(['x-goog-api-key' => $apiKey])
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
                'description' => 'Tim san pham theo ten/hang va khoang gia. Goi khi khach hoi tu van hoac so sanh dien thoai.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => ['type' => 'string'],
                        'min_price' => ['type' => 'number'],
                        'max_price' => ['type' => 'number'],
                    ],
                ],
            ],
            [
                'name' => 'get_revenue_stats',
                'description' => 'Thong ke doanh thu va so don theo khoang ngay (YYYY-MM-DD).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'from' => ['type' => 'string'],
                        'to' => ['type' => 'string'],
                    ],
                    'required' => ['from', 'to'],
                ],
            ],
            [
                'name' => 'top_selling_products',
                'description' => 'Danh sach san pham ban chay nhat.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer'],
                    ],
                ],
            ],
        ];
    }

    private function executeTool(string $name, array $input): string
    {
        return match ($name) {
            'search_products' => Product::query()
                ->where('is_active', true)
                ->when($input['keyword'] ?? null, function ($query, $keyword) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$keyword}%"));
                    });
                })
                ->when($input['min_price'] ?? null, fn ($query, $price) => $query->where('price', '>=', $price))
                ->when($input['max_price'] ?? null, fn ($query, $price) => $query->where('price', '<=', $price))
                ->orderByDesc('sold_count')
                ->limit(5)
                ->get(['id', 'name', 'slug', 'price', 'sale_price', 'sold_count', 'thumbnail'])
                ->map(function (Product $product): array {
                    return [
                        'name' => $product->name,
                        'price' => $product->price,
                        'sale_price' => $product->sale_price,
                        'sold_count' => $product->sold_count,
                        'url' => url('/products/' . $product->slug),
                        'thumbnail' => $this->resolveThumbnailUrl($product->thumbnail),
                    ];
                })
                ->toJson(JSON_UNESCAPED_UNICODE),

            'get_revenue_stats' => Order::whereBetween('created_at', [$input['from'], $input['to']])
                ->where('status', 'delivered')
                ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as revenue')
                ->first()
                ?->toJson() ?? json_encode(['total_orders' => 0, 'revenue' => 0], JSON_UNESCAPED_UNICODE),

            'top_selling_products' => OrderItem::selectRaw('product_id, MAX(product_name) as product_name, SUM(quantity) as sold')
                ->groupBy('product_id')
                ->orderByDesc('sold')
                ->limit((int) ($input['limit'] ?? 5))
                ->with('product:id,slug,thumbnail,price')
                ->get()
                ->map(function (OrderItem $item): array {
                    return [
                        'product_name' => $item->product_name,
                        'sold' => (int) $item->sold,
                        'price' => $item->product?->price,
                        'url' => $item->product ? url('/products/' . $item->product->slug) : null,
                        'thumbnail' => $this->resolveThumbnailUrl($item->product?->thumbnail),
                    ];
                })
                ->toJson(JSON_UNESCAPED_UNICODE),

            default => json_encode(['error' => "Unknown tool: {$name}"], JSON_UNESCAPED_UNICODE),
        };
    }

    private function resolveThumbnailUrl(?string $thumbnail): ?string
    {
        $thumbnail = trim((string) $thumbnail);

        if ($thumbnail === '') {
            return null;
        }

        if (str_starts_with($thumbnail, 'http://') || str_starts_with($thumbnail, 'https://')) {
            return $thumbnail;
        }

        $path = ltrim($thumbnail, '/');

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}
