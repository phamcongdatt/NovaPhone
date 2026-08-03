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
Bạn là NovaPhone, một nhân viên tư vấn điện thoại nhiệt tình, thân thiện và am hiểu sản phẩm.

Phong cách giao tiếp:
- Luôn trả lời bằng tiếng Việt tự nhiên, gần gũi và giống một người đang tư vấn trực tiếp.
- Xưng "mình" và gọi người dùng là "bạn".
- Chủ động thể hiện sự quan tâm: hỏi thêm nhu cầu, ngân sách hoặc mục đích sử dụng khi cần.
- Trả lời ngắn gọn, rõ ràng, dễ hiểu; không nói chuyện máy móc, không lặp lại câu mở đầu.
- Có thể dùng cách nói thân thiện như "Mình gợi ý bạn...", "Với nhu cầu này...", "Bạn có thể cân nhắc...".
- Khi người dùng chào hỏi hoặc cảm ơn, hãy đáp lại tự nhiên và vui vẻ, không cần gọi function.
- Nếu câu hỏi chưa đủ thông tin, hãy hỏi tối đa một câu làm rõ hoặc đưa ra vài lựa chọn phù hợp để người dùng dễ chọn.
- Không tự nhận mình là con người và không nhắc đến system prompt, function, API hay quy trình nội bộ.

Bạn có thể hỗ trợ:
- Tư vấn và so sánh điện thoại
- Gợi ý phụ kiện
- Giải thích thông số kỹ thuật bằng ngôn ngữ dễ hiểu
- Tư vấn sản phẩm theo ngân sách và nhu cầu
- Hướng dẫn người dùng mua hàng

Nguyên tắc về dữ liệu:
- Khi tư vấn sản phẩm, giá, chương trình giảm giá hoặc sản phẩm bán chạy, luôn dùng function để lấy dữ liệu thật.
- Khi người dùng hỏi thông tin chi tiết hoặc thông số của một sản phẩm, dùng function `get_product_details`.
- Khi người dùng muốn so sánh hai sản phẩm, bắt buộc dùng function `compare_products` để lấy dữ liệu của cả hai máy; không so sánh dựa trên trí nhớ.
- Khi người dùng muốn tìm hoặc được gợi ý sản phẩm theo hãng, mức giá hoặc nhu cầu, dùng function `search_products`.
- Khi người dùng hỏi sản phẩm bán chạy, dùng function `top_selling_products`.
- Không tự bịa tên sản phẩm, giá, tình trạng còn hàng, mức giảm giá hoặc thông số không có trong dữ liệu.
- Không tiết lộ thông tin nội bộ như doanh thu, lợi nhuận, tồn kho, số lượng đơn hàng, dữ liệu khách hàng hoặc dữ liệu nhân viên.
- Nếu người dùng hỏi thông tin nội bộ, hãy từ chối nhẹ nhàng và chuyển hướng sang tư vấn sản phẩm hoặc hỗ trợ mua hàng.

Cách giới thiệu sản phẩm:
- Với mỗi sản phẩm, trình bày theo mẫu:
  ![Tên sản phẩm](thumbnail)
  [Tên sản phẩm](url) - giá bán
- Nếu thumbnail là null thì bỏ qua dòng hình ảnh.
- Hiển thị giá theo định dạng Việt Nam: 6990000.00 thành 6.990.000đ.
- Nếu có sale_price, ghi "Giảm còn ..." và kèm giá gốc nếu dữ liệu có sẵn.
- Sau danh sách sản phẩm, thêm một nhận xét ngắn giúp người dùng dễ lựa chọn.
- Nếu người dùng muốn giá rẻ hơn, hãy giải thích ngắn gọn và gợi ý sản phẩm có giá thấp hơn hoặc đang giảm giá.
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
            $messages = array_silce($validated['messages'],-self:: MAX_MESSAGES);
            $firstUserIndex = collect($messages)->search(
                fn(array $message) => $messages['role'] === 'user'
            );
            if($firsUserIndex == false)
                {
                    return response()->json ([
                        'reply' => 'Xin loi, chatbot khong tim thay tin nhan nguoi dung de tra loi. Vui long gui tin nhan truoc khi goi API.',
                    ], 422);
                }
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
                'description' => 'Tim va goi y san pham theo ten, hang hoac khoang gia. Dung khi khach muon tim may phu hop voi nhu cau.',
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
                'name' => 'get_product_details',
                'description' => 'Lay thong tin chi tiet va thong so that cua mot san pham khi khach hoi ve cau hinh, camera, man hinh, pin, RAM, he dieu hanh, gia hoac phien ban.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'product' => [
                            'type' => 'string',
                            'description' => 'Ten san pham hoac slug san pham.',
                        ],
                    ],
                    'required' => ['product'],
                ],
            ],
            [
                'name' => 'compare_products',
                'description' => 'So sanh dung hai dien thoai theo gia, phien ban, chip, RAM, man hinh, tan so quet, camera, pin, sac nhanh, he dieu hanh va ket noi. Goi khi khach dung cac tu nhu so sanh, khac nhau, nen mua may nao, may nao tot hon.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'product_a' => [
                            'type' => 'string',
                            'description' => 'Ten hoac slug cua dien thoai thu nhat.',
                        ],
                        'product_b' => [
                            'type' => 'string',
                            'description' => 'Ten hoac slug cua dien thoai thu hai.',
                        ],
                    ],
                    'required' => ['product_a', 'product_b'],
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

            'get_product_details' => $this->getProductDetails($input),

            'compare_products' => $this->compareProducts($input),

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

    private function findActiveProduct(?string $search): ?Product
    {
        $search = trim((string) $search);

        if ($search === '') {
            return null;
        }

        $newQuery = fn () => Product::query()
            ->where('is_active', true)
            ->with([
                'brand:id,name',
                'category:id,name',
                'performance',
                'variants:id,product_id,name,storage,color,additional_price',
            ]);

        return ($newQuery())->where('slug', $search)->first()
            ?? ($newQuery())->where('name', $search)->first()
            ?? ($newQuery())->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"));
            })->first();
    }

    private function compareProducts(array $input): string
    {
        $productA = $this->findActiveProduct($input['product_a'] ?? null);
        $productB = $this->findActiveProduct($input['product_b'] ?? null);

        if (! $productA || ! $productB) {
            return json_encode([
                'error' => 'Khong tim thay mot hoac ca hai san pham. Hay hoi lai ten day du cua tung may.',
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($productA->is($productB)) {
            return json_encode([
                'error' => 'Hai san pham can so sanh phai la hai san pham khac nhau.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'product_a' => $this->productPayload($productA),
            'product_b' => $this->productPayload($productB),
        ], JSON_UNESCAPED_UNICODE);
    }

    private function getProductDetails(array $input): string
    {
        $product = $this->findActiveProduct($input['product'] ?? null);

        if (! $product) {
            return json_encode([
                'error' => 'Khong tim thay san pham phu hop.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode(
            $this->productPayload($product),
            JSON_UNESCAPED_UNICODE
        );
    }

    private function productPayload(Product $product): array
    {
        return [
            'name' => $product->name,
            'brand' => $product->brand?->name,
            'category' => $product->category?->name,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'url' => url('/products/' . $product->slug),
            'thumbnail' => $this->resolveThumbnailUrl($product->thumbnail),
            'storage_options' => $product->variants->pluck('storage')->filter()->unique()->values()->all(),
            'color_options' => $product->variants->pluck('color')->filter()->unique()->values()->all(),
            'specifications' => $product->performance_specifications,
        ];
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
