{{--
|--------------------------------------------------------------------------
| Features Block - Card Variant
|--------------------------------------------------------------------------
|
| Card-based layout with:
| - Icon (optional)
| - Title + Description
| - Hover interaction ready
|
--}}

<section class="py-16">

    <div class="container mx-auto px-6">

        {{-- Title --}}
        @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12">
            {{ $data['title'][$lang] ?? $data['title']['en'] ?? '' }}
        </h2>
        @endif

        {{-- Cards --}}
        <div class="grid gap-8
            {{ ($settings['columns'] ?? 3) == 2 ? 'md:grid-cols-2' : '' }}
            {{ ($settings['columns'] ?? 3) == 3 ? 'md:grid-cols-3' : '' }}
            {{ ($settings['columns'] ?? 3) == 4 ? 'md:grid-cols-4' : '' }}
        ">

            @foreach($data['items'] ?? [] as $item)

            <div class="group p-6 rounded-2xl border bg-white
                            hover:shadow-xl transition-all duration-300">

                {{-- Icon --}}
                @if(!empty($item['icon']))
                <div class="mb-4 text-2xl">
                    <i class="icon-{{ $item['icon'] }}"></i>
                </div>
                @endif

                {{-- Title --}}
                <h3 class="text-lg font-semibold">
                    {{ $item['title'][$lang] ?? '' }}
                </h3>

                {{-- Description --}}
                <p class="mt-2 text-gray-600">
                    {{ $item['description'][$lang] ?? '' }}
                </p>

            </div>

            @endforeach

        </div>

    </div>

</section>