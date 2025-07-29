@component('mail::message')
# 🔔 Low Inventory Alert

The following products have low inventory:

@foreach ($products as $product)
@if (is_object($product))
- **{{ $product->title }}** (SKU: {{ $product->sku }})  
  Quantity Left: **{{ $product->quantity }}**
@else
- {{ $product }} {{-- fallback to raw string --}}
@endif
@endforeach

Please restock soon.

Thanks,  
{{ config('app.name') }}
@endcomponent
