<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\TransactionDTO;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $paginator = $this->service->list($request->only(
            ['type', 'category', 'search', 'from', 'to', 'per_page', 'page']
        ));

        return TransactionResource::collection($paginator);
    }

    public function income(Request $request): AnonymousResourceCollection
    {
        $filters = array_merge($request->only(['search', 'from', 'to', 'per_page', 'page']), ['type' => 'income']);
        return TransactionResource::collection($this->service->list($filters));
    }

    public function expenses(Request $request): AnonymousResourceCollection
    {
        $filters = array_merge($request->only(['search', 'from', 'to', 'per_page', 'page']), ['type' => 'expense']);
        return TransactionResource::collection($this->service->list($filters));
    }

    public function refunds(Request $request): AnonymousResourceCollection
    {
        $filters = array_merge($request->only(['search', 'from', 'to', 'per_page', 'page']), ['type' => 'refund']);
        return TransactionResource::collection($this->service->list($filters));
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $dto         = TransactionDTO::fromRequest($request->validated(), $request->user()->id);
        $transaction = $this->service->create($dto);

        return (new TransactionResource($transaction))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): TransactionResource
    {
        return new TransactionResource($this->service->find($id));
    }

    public function update(UpdateTransactionRequest $request, int $id): TransactionResource
    {
        $transaction = $this->service->find($id);
        $dto         = TransactionDTO::fromRequest($request->validated(), $request->user()->id);
        return new TransactionResource($this->service->update($transaction, $dto));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($this->service->find($id));
        return response()->json(['message' => 'Deleted successfully']);
    }
}
