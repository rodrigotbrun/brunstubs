<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    properties: [
        new OA\Property(property: 'id', title: 'ID', type: 'string', readOnly: true, nullable: false),
		new OA\Property(property: 'company_id', type: 'string'),
		new OA\Property(property: 'account_id', type: 'string'),
		new OA\Property(property: 'description', type: 'string'),
		new OA\Property(property: 'type', type: 'mixed'),
		new OA\Property(property: 'effective_date', type: 'string'),
		new OA\Property(property: 'due_date', type: 'string'),
		new OA\Property(property: 'amount', type: 'int'),
		new OA\Property(property: 'amount_brl', type: 'int'),
		new OA\Property(property: 'amount_currency', type: 'string'),
		new OA\Property(property: 'exchange_rate', type: 'mixed'),
		new OA\Property(property: 'category_id', type: 'string'),
		new OA\Property(property: 'create_by_id', type: 'string'),
		new OA\Property(property: 'paid_at', type: 'string'),
		new OA\Property(property: 'installments', type: 'int'),
		new OA\Property(property: 'installment_group', type: 'string'),
		new OA\Property(property: 'customer_id', type: 'string'),
        new OA\Property(property: 'can', properties: [
            new OA\Property(property: 'update', type: 'boolean', readOnly: true, nullable: false),
            new OA\Property(property: 'delete', type: 'boolean', readOnly: true, nullable: false),
        ], readOnly: true, nullable: false),
        new OA\Property(property: 'created_at', type: 'string', readOnly: true, nullable: false),
        new OA\Property(property: 'updated_at', type: 'string', readOnly: true, nullable: false),
    ]
)]
/** @mixin Category */
class CategoryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->prefixed_id,
			'company_id' => $this->company_id,
			'account_id' => $this->account_id,
			'description' => $this->description,
			'type' => $this->type,
			'effective_date' => $this->effective_date,
			'due_date' => $this->due_date,
			'amount' => $this->amount,
			'amount_brl' => $this->amount_brl,
			'amount_currency' => $this->amount_currency,
			'exchange_rate' => $this->exchange_rate,
			'category_id' => $this->category_id,
			'create_by_id' => $this->create_by_id,
			'paid_at' => $this->paid_at,
			'installments' => $this->installments,
			'installment_group' => $this->installment_group,
			'customer_id' => $this->customer_id,
            'can' => [
                'update' => auth()->user()->can('update', $this->resource),
                'delete' => auth()->user()->can('delete', $this->resource),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
