@props(['cars'])

<x-app-layout>
    <main>
        <!-- New Cars -->
        <section>
            <div class="container">
                <div class="flex justify-between items-center">
                    <h2>My Favourite Cars</h2>
                    @if($cars->total() > 0)
                        <div class="pagination-summary">
                            <p>
                                Showing {{ $cars->firstItem() }} to {{ $cars->lastItem() }} of {{ $cars->total() }} results
                            </p>
                        </div>
                    @endif
                </div>
                <div class="car-items-listing">
                    @forelse($cars as $car)
                        <x-car-item :$car :isInWatchlist="true" />
                    @empty
                        <div class="text-center p-large">
                            There are no items in your watchlist.
                        </div>
                    @endforelse
                </div>

                {{ $cars->onEachSide(1)->links('pagination') }}

            </div>
        </section>
        <!--/ New Cars -->
    </main>
</x-app-layout>
