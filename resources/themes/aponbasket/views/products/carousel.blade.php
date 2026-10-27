{{--
    AponBasket product carousel — override of shop::components.products.carousel

    Same Vue component (v-products-carousel), props and behaviour as the core
    component, plus two optional presentation props: eyebrow and subtitle.
--}}
@props([
    'src'            => '',
    'title'          => '',
    'navigationLink' => '',
    'eyebrow'        => null,
    'subtitle'       => null,
])

<v-products-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    eyebrow="{{ $eyebrow }}"
    subtitle="{{ $subtitle }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-products-carousel-template"
    >
        <div
            class="ab-carousel"
            v-if="! isLoading && products.length"
        >
            <div class="ab-carousel__head">
                <div>
                    <span class="ab-eyebrow" v-if="eyebrow">@{{ eyebrow }}</span>

                    <h2 class="ab-title" v-if="title">@{{ title }}</h2>

                    <p class="ab-lead ab-carousel__subtitle" v-if="subtitle">@{{ subtitle }}</p>
                </div>

                <div class="ab-carousel__actions">
                    <a
                        :href="navigationLink"
                        class="ab-link-arrow"
                        v-if="navigationLink"
                    >
                        @lang('shop::app.components.products.carousel.view-all')

                        <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                    </a>

                    <div class="ab-rail-controls" v-if="products.length > 4">
                        <button
                            type="button"
                            class="ab-hero__arrow"
                            aria-label="@lang('shop::app.components.products.carousel.previous')"
                            @click="swipeLeft"
                        >
                            <svg width="17" height="17" aria-hidden="true"><use href="#ab-arrow-left"></use></svg>
                        </button>

                        <button
                            type="button"
                            class="ab-hero__arrow"
                            aria-label="@lang('shop::app.components.products.carousel.next')"
                            @click="swipeRight"
                        >
                            <svg width="17" height="17" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div
                ref="swiperContainer"
                class="ab-rail"
            >
                <x-shop::products.card
                    v-for="product in products"
                    :key="product.id"
                />
            </div>

            <div class="ab-rail-more" v-if="navigationLink">
                <a :href="navigationLink" class="ab-btn ab-btn--ghost">
                    @lang('shop::app.components.products.carousel.view-all')

                    <svg width="16" height="16" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                </a>
            </div>
        </div>

        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>
    </script>

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'eyebrow',
                'subtitle',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    products: [],
                };
            },

            mounted() {
                this.getProducts();
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.products = response.data.data;
                        }).catch(error => {
                            this.isLoading = false;

                            console.log(error);
                        });
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    if (! container) {
                        return;
                    }

                    container.scrollLeft -= this.step();
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    if (! container) {
                        return;
                    }

                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
                        container.scrollLeft = 0;
                    } else {
                        container.scrollLeft += this.step();
                    }
                },

                step() {
                    return Math.max(240, Math.round(window.innerWidth * 0.75));
                },
            },
        });
    </script>
@endpushOnce
