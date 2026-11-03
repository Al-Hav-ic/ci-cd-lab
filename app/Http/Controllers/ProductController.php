<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $items = [
        [
            'id' => 1,
            'name' => 'Молоко «Галичина» 2.5 % 900 мл',
            'price' => 32.50,
            'stock_qty' => 48,
            'manufacturer' => 'Галичина',
        ],
        [
            'id' => 2,
            'name' => 'Хліб «Київхліб» нарізаний',
            'price' => 28.90,
            'stock_qty' => 25,
            'manufacturer' => 'Київхліб',
        ],
        [
            'id' => 3,
            'name' => 'Олія соняшникова «Олейна» 1 л',
            'price' => 62.00,
            'stock_qty' => 0,
            'manufacturer' => 'Олейна',
        ],
    ];

    public function index(Request $request): JsonResponse
    {
        $manufacturer = $request->query('manufacturer');

        $items = $manufacturer === null
            ? $this->items
            : array_values(array_filter(
                $this->items,
                static fn (array $item): bool => $item['manufacturer'] === $manufacturer,
            ));

        return response()->json([
            'status' => 'success',
            'count' => count($items),
            'data' => $items,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'manufacturer' => ['required', 'string', 'max:120'],
        ]);

        $nextId = $this->items === []
            ? 1
            : (int) max(array_column($this->items, 'id')) + 1;

        $item = [
            'id' => $nextId,
            'name' => $validated['name'],
            'price' => (float) $validated['price'],
            'stock_qty' => (int) $validated['stock_qty'],
            'manufacturer' => $validated['manufacturer'],
        ];

        $this->items[] = $item;

        return response()->json([
            'status' => 'created',
            'data' => $item,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $itemId = (int) $id;

        foreach ($this->items as $item) {
            if ($item['id'] === $itemId) {
                return response()->json([
                    'status' => 'success',
                    'data' => $item,
                ], 200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Ресурс із зазначеним ідентифікатором не знайдено.',
        ], 404);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'status' => 'not_implemented',
            'message' => 'HTML-форма буде реалізована в ЛР №7.',
        ], 501);
    }

    public function edit(string $id): JsonResponse
    {
        return $this->create();
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'status' => 'not_implemented',
            'message' => 'Оновлення буде реалізоване в ЛР №6.',
        ], 501);
    }

    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'status' => 'not_implemented',
            'message' => 'Видалення буде реалізоване в ЛР №6.',
        ], 501);
    }
}
