<div class="row gutters-16">
    @php
    $physical = false;
    $col_val = 'col-12';
    foreach ($products as $key => $cartItem){
    $product = get_single_product($cartItem);
    if ($product->digital == 0) {
    $physical = true;
    $col_val = 'col-12';
    }
    }
    @endphp
    <!-- Product List -->
    <div class="{{ $col_val }}">
        <ul class="list-group list-group-flush">
            @php
            $total = 0;
            $admin_products = array();
            $seller_products = array();
            $admin_product_variation = array();
            $seller_product_variation = array();
            foreach ($carts as $key => $cartItem){
                $product = get_single_product($cartItem['product_id']);

                if($product->added_by == 'admin'){
                    array_push($admin_products, $cartItem['product_id']);
                    $admin_product_variation[] = $cartItem['variation'];
                }
                else{
                    $product_ids = array();
                    if(isset($seller_products[$product->user_id])){
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem['product_id']);
                    $seller_products[$product->user_id] = $product_ids;
                    $seller_product_variation[$product->user_id][] = $cartItem['variation'];
                }
            }
        @endphp
                <!-- Inhouse Products -->
            @if (!empty($admin_products))
                @php
                    $all_admin_products = true;
                    if(count($admin_products) != count($carts->toQuery()->active()->whereIn('product_id', $admin_products)->get())){
                        $all_admin_products = false;
                    }
                @endphp
                @foreach ($admin_products as $key => $product_id)
                    @php
                        $product = get_single_product($product_id);
                        $cartItem = $carts->toQuery()->where('product_id', $product_id)->where('variation', $admin_product_variation[$key])->first();
                        $product_stock = $product->stocks->where('variant', $cartItem->variation)->first();
                        $total = $total + cart_product_price($cartItem, $product, false) * $cartItem->quantity;
                    @endphp
                    <li class="list-group-item px-0 border-md-0">
                        <div class="row gutters-5 align-items-center">
                            <!-- Product Image & name -->
                            <div class="col-md-5 col-10 d-flex align-items-center mb-2 mb-md-0">
                                <span class="mr-2 ml-0">
                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="img-fit size-64px" alt="{{ $product->getTranslation('name')  }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </span>
                                <span>
                                    <span class="fs-14 fw-400 text-dark text-truncate-2 mb-2">{{ $product->getTranslation('name') }}</span>
                                    @if ($admin_product_variation[$key] != '')
                                        <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $admin_product_variation[$key] }}</span>
                                    @endif
                                </span>
                            </div>
                            <!-- Price & Tax -->
                            <div class="col-md col-4 ml-4 ml-sm-0 my-3 my-md-0 d-flex flex-column ml-sm-5 ml-md-0">
                                <span class="fs-12 text-secondary">{{ translate('Price')}}</span>
                                <span class="fw-700 fs-14 mb-2">{{ cart_product_price($cartItem, $product, true, false) }}</span>
                                <span>
                                    <span class="opacity-90 fs-12">{{ translate('Tax')}}: {{ cart_product_tax($cartItem, $product) }}</span>
                                </span>
                            </div>
                            <!-- Quantity & Total -->
                            <div class="col-xl-4 col-md-3 col d-flex flex-column flex-xl-row justify-content-xl-between align-items-xl-center">
                                <!-- Quantity -->
                                <div>
                                    @if ($product->digital != 1 && $product->auction_product == 0)
                                        <div class="d-flex flex-xl-column flex-xxl-row align-items-center aiz-plus-minus mr-0 ml-0" style="width: max-content !important;">
                                            <button
                                                class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                type="button" data-type="plus"
                                                data-field="quantity[{{ $cartItem->id }}]">
                                                <i class="las la-plus"></i>
                                            </button>
                                            <input type="number" name="quantity[{{ $cartItem->id }}]"
                                                   class="col border-0 text-center px-0 fs-14 input-number"
                                                   placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                   min="{{ $product->min_qty }}"
                                                   max="{{ $product_stock->qty }}"
                                                   onchange="updateCheckoutQuantity({{ $cartItem->id }}, this)" style="min-width: 45px;">
                                            <button
                                                class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                type="button" data-type="minus"
                                                data-field="quantity[{{ $cartItem->id }}]">
                                                <i class="las la-minus"></i>
                                            </button>
                                        </div>
                                    @elseif($product->auction_product == 1)
                                        <span class="fw-700 fs-14">1</span>
                                    @endif
                                </div>
                                <!-- Total -->
                                <div class="mr-2 mt-2 mt-xl-0">
                                    <span class="fw-700 fs-14 text-primary">{{ single_price(cart_product_price($cartItem, $product, false) * $cartItem->quantity) }}</span>
                                </div>
                            </div>
                            <!-- Remove From Cart -->
                            <div class="col-auto text-right">
                                <a href="javascript:void(0)" onclick="removeFromCheckout(event, {{ $cartItem->id }})" class="btn btn-icon btn-sm bg-white hov-svg-danger" title="{{ translate('Remove') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12.27" height="16" viewBox="0 0 12.27 16">
                                        <g id="Group_23970" data-name="Group 23970" transform="translate(-1332 -420)">
                                            <path id="Path_28714" data-name="Path 28714" d="M17.9,9.037l-.258,7.8a2.569,2.569,0,0,1-2.577,2.485h-4.9A2.569,2.569,0,0,1,7.587,16.84l-.258-7.8a.645.645,0,0,1,1.289-.043l.258,7.8a1.289,1.289,0,0,0,1.289,1.239h4.9a1.289,1.289,0,0,0,1.289-1.241l.258-7.8a.645.645,0,0,1,1.289.043Zm.852-2.6a.644.644,0,0,1-.644.644H7.122a.644.644,0,1,1,0-1.289h2a.822.822,0,0,0,.82-.74,1.927,1.927,0,0,1,1.922-1.736h1.5a1.927,1.927,0,0,1,1.922,1.736.822.822,0,0,0,.82.74h2a.644.644,0,0,1,.644.644ZM11.058,5.8h3.11A2.126,2.126,0,0,1,14,5.189a.644.644,0,0,0-.64-.58h-1.5a.644.644,0,0,0-.64.58,2.126,2.126,0,0,1-.165.608Zm.649,9.761V10.072a.644.644,0,0,0-1.289,0v5.488a.644.644,0,0,0,1.289,0Zm3.1,0V10.072a.644.644,0,1,0-1.289,0v5.488a.644.644,0,1,0,1.289,0Z" transform="translate(1325.522 416.678)" fill="#9d9da6"/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
{{--        <ul class="list-group list-group-flush mb-3">--}}
{{--            @foreach ($products as $key => $cartItem)--}}
{{--            @php--}}
{{--            $product = get_single_product($cartItem);--}}
{{--            @endphp--}}
{{--            <li class="list-group-item pl-0 py-3 border-0">--}}
{{--                <div class="d-flex align-items-center">--}}
{{--                    <span class="mr-2 mr-md-3">--}}
{{--                        <img src="{{ get_image($product->thumbnail) }}"--}}
{{--                            class="img-fit size-60px"--}}
{{--                            alt="{{  $product->getTranslation('name')  }}"--}}
{{--                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">--}}
{{--                    </span>--}}
{{--                    <span class="fs-14 fw-400 text-dark">--}}
{{--                        <span class="text-truncate-2">{{ $product->getTranslation('name') }}</span>--}}
{{--                        @if ($product_variation[$key] != '')--}}
{{--                        <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $product_variation[$key] }}</span>--}}
{{--                        @endif--}}
{{--                    </span>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
    </div>

{{--    @if ($physical)--}}
{{--    <!-- Choose Delivery Type -->--}}
{{--    <div class="col-md-6 mb-2">--}}
{{--        <h6 class="fs-14 fw-700 mt-3">{{ translate('Choose Delivery Type') }}</h6>--}}
{{--        <div class="row gutters-16">--}}
{{--            <!-- Home Delivery -->--}}
{{--            @if (get_setting('shipping_type') != 'carrier_wise_shipping')--}}
{{--            <div class="col-6">--}}
{{--                <label class="aiz-megabox d-block bg-white mb-0">--}}
{{--                    <input--}}
{{--                        type="radio"--}}
{{--                        name="shipping_type_{{ $owner_id }}"--}}
{{--                        value="home_delivery"--}}
{{--                        onchange="show_pickup_point(this, {{ $owner_id }})"--}}
{{--                        data-target=".pickup_point_id_{{ $owner_id }}"--}}
{{--                        checked required>--}}
{{--                    <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">--}}
{{--                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-600">{{ translate('Home Delivery') }}</span>--}}
{{--                    </span>--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            <!-- Carrier -->--}}
{{--            @else--}}
{{--            <div class="col-6">--}}
{{--                <label class="aiz-megabox d-block bg-white mb-0">--}}
{{--                    <input--}}
{{--                        type="radio"--}}
{{--                        name="shipping_type_{{ $owner_id }}"--}}
{{--                        value="carrier"--}}
{{--                        class="shipping-type-radio"--}}
{{--                        data-owner="{{ $owner_id }}"--}}
{{--                        onchange="show_pickup_point(this, {{ $owner_id }})"--}}
{{--                        data-target=".pickup_point_id_{{ $owner_id }}"--}}
{{--                        checked required>--}}
{{--                    <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">--}}
{{--                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-600">{{ translate('Carrier') }}</span>--}}
{{--                    </span>--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            @endif--}}
{{--            <!-- Local Pickup -->--}}
{{--            @if ($pickup_point_list)--}}
{{--            <div class="col-6">--}}
{{--                <label class="aiz-megabox d-block bg-white mb-0">--}}
{{--                    <input--}}
{{--                        type="radio"--}}
{{--                        name="shipping_type_{{ $owner_id }}"--}}
{{--                        value="pickup_point"--}}
{{--                        class="shipping-type-radio"--}}
{{--                        data-owner="{{ $owner_id }}"--}}
{{--                        onchange="show_pickup_point(this, {{ $owner_id }})"--}}
{{--                        data-target=".pickup_point_id_{{ $owner_id }}"--}}
{{--                        required>--}}
{{--                    <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">--}}
{{--                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-600">{{ translate('Local Pickup') }}</span>--}}
{{--                    </span>--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            @endif--}}
{{--        </div>--}}

{{--        <!-- Pickup Point List -->--}}
{{--        @if ($pickup_point_list)--}}
{{--        <div class="mt-3 pickup_point_id_{{ $owner_id }} d-none">--}}
{{--            <select--}}
{{--                class="form-control aiz-selectpicker rounded-0"--}}
{{--                name="pickup_point_id_{{ $owner_id }}"--}}
{{--                data-live-search="true"--}}
{{--                onchange="updateDeliveryInfo('pickup_point', this.value, {{ $owner_id }})">--}}
{{--                <option value="">{{ translate('Select your nearest pickup point')}}</option>--}}
{{--                @foreach ($pickup_point_list as $pick_up_point)--}}
{{--                <option--}}
{{--                    value="{{ $pick_up_point->id }}"--}}
{{--                    data-content="<span class='d-block'>--}}
{{--                                                <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>--}}
{{--                                                <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>--}}
{{--                                                <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>--}}
{{--                                            </span>">--}}
{{--                </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
{{--        @endif--}}

{{--        <!-- Carrier Wise Shipping -->--}}
{{--        @if (get_setting('shipping_type') == 'carrier_wise_shipping')--}}

{{--        <div class="row pt-3 carrier_id_{{ $owner_id }}">--}}
{{--            @if($carrier_list->isEmpty())--}}

{{--            <div class="col-md-12">--}}
{{--                <div class="alert alert-danger col-md-12 mb-2">--}}
{{--                    <strong>{{ translate('Shipping is not available to your selected address.') }}</strong><br>--}}
{{--                    {{ translate('Please choose a different address.') }}--}}
{{--                </div>--}}
{{--                <span class="shipping-unavailable-flag" style="display: none;"></span>--}}
{{--            </div>--}}


{{--            @else--}}
{{--            @foreach($carrier_list as $carrier_key => $carrier)--}}
{{--            <div class="col-md-12 mb-2">--}}
{{--                <label class="aiz-megabox d-block bg-white mb-0">--}}
{{--                    <input--}}
{{--                        type="radio"--}}
{{--                        name="carrier_id_{{ $owner_id }}"--}}
{{--                        value="{{ $carrier->id }}"--}}
{{--                        @if($carrier_key==0) checked @endif--}}
{{--                        onchange="updateDeliveryInfo('carrier', {{ $carrier->id }}, {{ $owner_id }})">--}}
{{--                    <span class="d-flex flex-wrap p-3 aiz-megabox-elem rounded-0">--}}
{{--                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-600">--}}
{{--                            <img src="{{ uploaded_asset($carrier->logo)}}" alt="Image" class="w-50px img-fit">--}}
{{--                        </span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-700">{{ $carrier->name }}</span>--}}
{{--                        <span class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in').' '.$carrier->transit_time }}</span>--}}
{{--                        <span class="flex-grow-1 pl-4 pl-sm-3 fw-600 mt-2 mt-sm-0 text-sm-right">{{ single_price(carrier_base_price($carts, $carrier->id, $owner_id, $shipping_info)) }}</span>--}}
{{--                    </span>--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            @endforeach--}}
{{--            @endif--}}
{{--        </div>--}}

{{--        @endif--}}
{{--    </div>--}}
{{--    @endif--}}
</div>
