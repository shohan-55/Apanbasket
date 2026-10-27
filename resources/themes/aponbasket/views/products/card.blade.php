{{--
    AponBasket product card — override of shop::components.products.card

    Keeps the same Vue component name (v-product-card), template id, props and
    events as the core component, so category pages, carousels, wishlists and
    search results keep working.
--}}
<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid card -->
        <article
            class="ab-card"
            v-if="mode != 'list'"
        >
            <a
                class="ab-card__media"
                :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                :aria-label="product.name"
            >
                <img
                    class="ab-card__img"
                    :src="product.base_image.medium_image_url"
                    :alt="product.base_image.alt || product.name"
                    width="300"
                    height="300"
                    loading="lazy"
                >

                <span class="ab-card__badges">
                    <span
                        class="ab-badge ab-badge--sale"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </span>

                    <span
                        class="ab-badge ab-badge--new"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </span>
                </span>
            </a>

            <div class="ab-card__tools">
                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <button
                        type="button"
                        class="ab-card__tool"
                        :class="{'is-active': product.is_wishlist}"
                        role="button"
                        tabindex="0"
                        aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        @click="addToWishlist()"
                    >
                        <svg width="15" height="15" aria-hidden="true" v-if="! product.is_wishlist"><use href="#ab-heart"></use></svg>
                        <svg width="15" height="15" aria-hidden="true" v-else><use href="#ab-heart-fill"></use></svg>
                    </button>
                @endif

                @if (core()->getConfigData('catalog.products.settings.compare_option'))
                    <button
                        type="button"
                        class="ab-card__tool"
                        role="button"
                        tabindex="0"
                        aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                        @click="addToCompare(product.id)"
                    >
                        <svg width="15" height="15" aria-hidden="true"><use href="#ab-globe"></use></svg>
                    </button>
                @endif

                <button
                    type="button"
                    class="ab-card__tool"
                    role="button"
                    tabindex="0"
                    aria-label="Quick add to cart"
                    :disabled="! product.is_saleable || isAddingToCart"
                    @click="addToCart()"
                >
                    <svg width="15" height="15" aria-hidden="true"><use href="#ab-cart"></use></svg>
                </button>
            </div>

            <div class="ab-card__body">
                <h3 class="ab-card__name">
                    <a
                        :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                        v-text="product.name"
                    ></a>
                </h3>

                <div
                    class="ab-card__rating"
                    v-if="product.ratings.total || product.reviews.total"
                >
                    <span class="ab-card__stars">
                        <svg width="11" height="11" aria-hidden="true" v-for="star in 5" :key="star"><use href="#ab-star"></use></svg>
                    </span>

                    <span>
                        (@{{ product.ratings.total || product.reviews.total }})
                    </span>
                </div>

                <div class="ab-card__rating" v-else>
                    <span class="ab-card__stars" style="color: #d8d2c2;">
                        <svg width="11" height="11" aria-hidden="true" v-for="star in 5" :key="star"><use href="#ab-star"></use></svg>
                    </span>

                    <span>@lang('shop::app.components.products.card.review-description')</span>
                </div>

                <div class="ab-card__foot">
                    <div class="ab-card__price" v-html="product.price_html"></div>

                    <button
                        type="button"
                        class="ab-card__add"
                        aria-label="@lang('shop::app.components.products.card.add-to-cart')"
                        :disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    >
                        <svg width="17" height="17" aria-hidden="true"><use href="#ab-cart"></use></svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- List card -->
        <article
            class="ab-card ab-card--list"
            v-else
        >
            <a
                class="ab-card__media"
                :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                :aria-label="product.name"
            >
                <img
                    class="ab-card__img"
                    :src="product.base_image.medium_image_url"
                    :alt="product.base_image.alt || product.name"
                    width="300"
                    height="300"
                    loading="lazy"
                >

                <span class="ab-card__badges">
                    <span class="ab-badge ab-badge--sale" v-if="product.on_sale">
                        @lang('shop::app.components.products.card.sale')
                    </span>

                    <span class="ab-badge ab-badge--new" v-else-if="product.is_new">
                        @lang('shop::app.components.products.card.new')
                    </span>
                </span>
            </a>

            <div class="ab-card__body">
                <h3 class="ab-card__name">
                    <a
                        :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                        v-text="product.name"
                    ></a>
                </h3>

                <div class="ab-card__price" v-html="product.price_html"></div>

                <div class="ab-card__foot">
                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        <button
                            type="button"
                            class="ab-btn ab-btn--primary ab-btn--sm"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        >
                            @lang('shop::app.components.products.card.add-to-cart')
                        </button>
                    @endif

                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                        <button
                            type="button"
                            class="ab-card__tool"
                            :class="{'is-active': product.is_wishlist}"
                            aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                            @click="addToWishlist()"
                        >
                            <svg width="15" height="15" aria-hidden="true"><use href="#ab-heart"></use></svg>
                        </button>
                    @endif
                </div>
            </div>
        </article>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                }
            },

            methods: {
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                'product_id': productId
                            })
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {
                                if ([400, 422].includes(error.response.status)) {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                    return;
                                }

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                            });

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (! value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                addToCart() {
                    this.isAddingToCart = true;

                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data );

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }

                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce
