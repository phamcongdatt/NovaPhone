<?php

namespace App\Console\Commands;

use App\Models\AdministrativeProvince;
use App\Models\AdministrativeWard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use RuntimeException;

class SyncAdministrativeLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-administrative-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ danh mục tỉnh/thành và xã/phường Việt Nam sau sáp nhập 2025';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim((string) config('services.administrative.base_url'), '/');
        $sourceVersion = (string) config('services.administrative.source_version');
        $caBundle = config('services.administrative.ca_bundle');
        $http = Http::acceptJson();

        if (is_string($caBundle) && is_file($caBundle)) {
            $http = $http->withOptions(['verify' => $caBundle]);
        }

        try {
            $sourceProvinces = $http
                ->connectTimeout(10)
                ->timeout(30)
                ->retry(3, 500)
                ->get("{$baseUrl}/p/")
                ->throw()
                ->json();

            if (! is_array($sourceProvinces) || count($sourceProvinces) < 30) {
                throw new RuntimeException('API không trả về đủ danh sách tỉnh/thành phố.');
            }

            $now = now();
            $provinces = [];
            $wards = [];

            foreach ($sourceProvinces as $sourceProvince) {
                $rawProvinceCode = data_get($sourceProvince, 'code');
                $provinceName = trim((string) data_get($sourceProvince, 'name'));

                if ($rawProvinceCode === null || $provinceName === '') {
                    throw new RuntimeException('API trả về một tỉnh/thành phố không hợp lệ.');
                }

                $provinceCode = str_pad((string) $rawProvinceCode, 2, '0', STR_PAD_LEFT);
                $provinces[$provinceCode] = [
                    'code' => $provinceCode,
                    'name' => $provinceName,
                    'type' => data_get($sourceProvince, 'division_type'),
                    'is_active' => true,
                    'source_version' => $sourceVersion,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $provinceDetail = $http
                    ->connectTimeout(10)
                    ->timeout(30)
                    ->retry(3, 500)
                    ->get("{$baseUrl}/p/{$rawProvinceCode}", ['depth' => 2])
                    ->throw()
                    ->json();

                $sourceWards = data_get($provinceDetail, 'wards');

                if (! is_array($sourceWards)) {
                    throw new RuntimeException("API không trả về danh sách xã/phường của {$provinceName}.");
                }

                foreach ($sourceWards as $sourceWard) {
                    $rawWardCode = data_get($sourceWard, 'code');
                    $wardName = trim((string) data_get($sourceWard, 'name'));

                    if ($rawWardCode === null || $wardName === '') {
                        throw new RuntimeException("API trả về xã/phường không hợp lệ của {$provinceName}.");
                    }

                    $wardCode = str_pad((string) $rawWardCode, 5, '0', STR_PAD_LEFT);
                    $wards[$wardCode] = [
                        'code' => $wardCode,
                        'province_code' => $provinceCode,
                        'name' => $wardName,
                        'type' => data_get($sourceWard, 'division_type'),
                        'is_active' => true,
                        'source_version' => $sourceVersion,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (count($provinces) !== 34 || count($wards) < 3000) {
                throw new RuntimeException(sprintf(
                    'Dữ liệu nguồn không đủ để đồng bộ an toàn (%d tỉnh/thành, %d xã/phường).',
                    count($provinces),
                    count($wards),
                ));
            }

            DB::transaction(function () use ($provinces, $wards, $sourceVersion, $now): void {
                AdministrativeProvince::upsert(
                    array_values($provinces),
                    ['code'],
                    ['name', 'type', 'is_active', 'source_version', 'updated_at'],
                );

                foreach (array_chunk(array_values($wards), 500) as $wardChunk) {
                    AdministrativeWard::upsert(
                        $wardChunk,
                        ['code'],
                        ['province_code', 'name', 'type', 'is_active', 'source_version', 'updated_at'],
                    );
                }

                AdministrativeProvince::query()
                    ->whereNotIn('code', array_keys($provinces))
                    ->update([
                        'is_active' => false,
                        'source_version' => $sourceVersion,
                        'updated_at' => $now,
                    ]);

                AdministrativeWard::query()
                    ->whereNotIn('code', array_keys($wards))
                    ->update([
                        'is_active' => false,
                        'source_version' => $sourceVersion,
                        'updated_at' => $now,
                    ]);
            });

            $this->info(sprintf(
                'Đã đồng bộ %d tỉnh/thành phố và %d xã/phường.',
                count($provinces),
                count($wards),
            ));

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            report($exception);
            $this->error('Đồng bộ thất bại: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
