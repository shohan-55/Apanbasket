{{--
    Builds the category menu once per request.
    Include it before rendering the navigation so $abMenuCategories is available.

    When the store has no categories yet the navigation falls back to
    Home / Shop all / Contact, so the header never renders empty.
--}}
@php
    if (! isset($abMenuCategories)) {
        $abMenuCategories = [];

        try {
            $abCategoryTree = app(\Webkul\Category\Repositories\CategoryRepository::class)
                ->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

            foreach ($abCategoryTree as $abCategory) {
                $abChildren = [];

                foreach ($abCategory->children as $abChild) {
                    $abChildren[] = [
                        'name' => $abChild->name,
                        'url'  => $abChild->url,
                    ];
                }

                $abMenuCategories[] = [
                    'name'     => $abCategory->name,
                    'url'      => $abCategory->url,
                    'children' => $abChildren,
                ];
            }
        } catch (\Throwable $e) {
            $abMenuCategories = [];
        }

        /* Keep the header readable: at most 7 top level entries. */
        $abMenuCategories = array_slice($abMenuCategories, 0, 7);
    }
@endphp
