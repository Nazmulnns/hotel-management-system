<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\GetInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function getInvoices(GetInvoiceRequest $request): AnonymousResourceCollection
    {
        return InvoiceResource::collection($this->invoiceService->getInvoices($request->validated()));
    }

    public function findInvoiceById(int $id): InvoiceResource|JsonResponse
    {
        $invoice = $this->invoiceService->findInvoiceById($id);

        if (empty($invoice)) {
            return response()->json(['message' => "No invoice found with id {$id}"], 404);
        }

        return new InvoiceResource($invoice);
    }

    public function generateInvoice(int $id): InvoiceResource|JsonResponse
    {
        $booking = Booking::with(['rooms', 'discounts'])->find($id);

        if (empty($booking)) {
            return response()->json(['message' => "No booking found with id {$id}"], 404);
        }

        return (new InvoiceResource($this->invoiceService->generateInvoice($booking)))
            ->additional(['message' => 'Invoice created successfully.']);
    }

    public function updateInvoiceStatus(UpdateInvoiceStatusRequest $request, int $id): InvoiceResource|JsonResponse
    {
        $invoice = $this->invoiceService->findInvoiceById($id);

        if (empty($invoice)) {
            return response()->json(['message' => "No invoice found with id {$id}"], 404);
        }

        $newStatus = InvoiceStatus::from($request->validated('status'));

        return (new InvoiceResource($this->invoiceService->updateInvoiceStatus($invoice, $newStatus)))
            ->additional(['message' => "Invoice is now {$newStatus->label()}."]);
    }
}