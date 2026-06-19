<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductSearchService
{
    /**
     * Perform weighted full-text search on products.
     *
     * @param string $query The raw search term.
     * @param int $limit Number of results to return (including primary + similar).
     * @return array An array of product objects with a calculated relevance score.
     */
    public function search(string $query, int $limit = 6): array
    {
        $normalized = $this->preprocessQuery($query);
        if (empty($normalized)) {
            return [];
        }

        // Split into words for keyword matching
        $words = array_filter(explode(' ', $normalized));
        if (empty($words)) {
            return [];
        }

        // 1. Prepare Fulltext search terms (MySQL BOOLEAN MODE wildcards)
        // E.g. "z flip" -> "z* flip*"
        $ftTerms = [];
        foreach ($words as $word) {
            // Include wildcards for words of at least 2 characters (to avoid parser bugs on extremely short queries)
            if (mb_strlen($word) >= 2) {
                $ftTerms[] = $word . '*';
            }
        }
        $ftQuery = implode(' ', $ftTerms);

        // 2. Perform DB query using a hybrid approach
        // We combine Fulltext MATCH scores with LIKE pattern matches for short terms (like 'z' or 's24')
        $queryBuilder = DB::table('products')
            ->select('products.*')
            ->selectRaw("
                (
                    (CASE WHEN ? = 1 THEN (MATCH(name) AGAINST(? IN BOOLEAN MODE) * 15) ELSE 0 END) +
                    (CASE WHEN ? = 1 THEN (MATCH(description) AGAINST(? IN BOOLEAN MODE) * 3) ELSE 0 END) +
                    (CASE WHEN name LIKE ? THEN 25 ELSE 0 END) +
                    (CASE WHEN description LIKE ? THEN 5 ELSE 0 END)
                ) AS relevance
            ", [
                !empty($ftQuery) ? 1 : 0, $ftQuery,
                !empty($ftQuery) ? 1 : 0, $ftQuery,
                '%' . $normalized . '%',
                '%' . $normalized . '%',
            ]);

        // Apply matching conditions
        $queryBuilder->where(function($q) use ($ftQuery, $words) {
            // 1. Matches Fulltext if available
            if (!empty($ftQuery)) {
                $q->orWhereRaw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [$ftQuery]);
            }
            
            // 2. Matches all keywords individually via LIKE (Hybrid/Tolerance fallback)
            $q->orWhere(function($subQ) use ($words) {
                foreach ($words as $word) {
                    $subQ->where(function($wordQ) use ($word) {
                        $wordQ->where('name', 'LIKE', '%' . $word . '%')
                              ->orWhere('description', 'LIKE', '%' . $word . '%');
                    });
                }
            });
        });

        // Filter only active, non-deleted products
        $queryBuilder->whereNull('deleted_at')
                     ->where('is_active', 1);

        // Sort by calculated relevance score and limit results
        return $queryBuilder->orderBy('relevance', 'desc')
                            ->limit($limit)
                            ->get()
                            ->toArray();
    }

    /**
     * Preprocess and normalize the search query.
     */
    private function preprocessQuery(string $query): string
    {
        // Lowercase and trim
        $query = mb_strtolower(trim($query));

        // Normalize capacity suffixes: e.g. "512g" or "512 g" -> "512gb"
        $query = preg_replace('/\b(\d+)\s*g\b/i', '$1gb', $query);

        // Remove special characters that could break MySQL full-text parsing
        $query = preg_replace('/[\+\-\<\>\(\)~*\"@]/', ' ', $query);

        // Normalize multiple spaces
        $query = preg_replace('/\s+/', ' ', $query);

        return trim($query);
    }
}
?>
