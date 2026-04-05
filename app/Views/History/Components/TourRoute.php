<?php
/** @var App\CmsModels\PageSection|null $tourRoute */
$stopsData = ($tourRoute && $tourRoute->content_html)
    ? json_decode($tourRoute->content_html, true)
    : [];
$routeStops = array_column($stopsData, 'name');
$totalStops = count($routeStops);
?>

<section class="grid md:grid-cols-2 gap-8 md:gap-x-12 items-stretch mb-20 mt-10">

    <div class="flex flex-col h-full">
        
        <div class="mb-6">
            <h2 class="font-history-serif text-[1.75rem] md:text-[2.25rem] text-ink-900">
                Map
            </h2>
            <div class="underline-history"></div>
        </div>

        <div class="w-full flex-grow min-h-[300px] md:min-h-[500px]">
            <iframe width='550' height='450' style='border:0' loading='lazy' src='https://mapforge.org/m/11cd638c?nomenu=true'></iframe>
        </div>
    </div>

    <div class="flex flex-col h-full">

        <div class="mb-6">
            <h2 class="font-history-serif text-[1.75rem] md:text-[2.25rem] text-ink-900">
                Route
            </h2>
            <div class="underline-history"></div>
        </div>

        <div class="p-[2rem] flex-grow flex flex-col justify-center">

            <div class="relative w-full py-4">

                <ol class="flex flex-col gap-10 md:gap-12 relative m-0 p-0">
                    <?php foreach ($routeStops as $index => $stop): ?>
                        <?php
                            $isLeft = $index % 2 === 0; //calculate pair or odd index for the stop positions
                            $isLast = $index === $totalStops - 1;

                            //circle position: left 45% for even, left 55% for odd
                            $circlePos = $isLeft ? 'md:left-[45%]' : 'md:left-[55%]';

                            //text right for even, left for odd
                            $textContainerClass = $isLeft
                                ? 'md:w-[45%] md:pr-6 md:justify-end text-left md:text-right'
                                : 'md:w-[45%] md:ml-[55%] md:pl-6 md:justify-start text-left';
                        ?>

                        <li class="relative flex items-start w-full group min-h-[44px] <?= $index >= 6 ? 'extra-stop hidden' : '' ?>">

                            <?php if (!$isLast): ?>
                                <svg class="absolute md:hidden z-0 overflow-visible" style="top: 22px; left: 54px; width: 2px; height: calc(100% + 2.5rem);">
                                    <line x1="0" y1="0" x2="0" y2="100%" stroke="black" stroke-width="3" stroke-dasharray="4 14" stroke-linecap="round" />
                                </svg>

                                <svg class="hidden md:block absolute z-0 overflow-visible" style="top: 22px; left: 45%; width: 10%; height: calc(100% + 3rem);">
                                    <?php if ($isLeft): ?>
                                        <line x1="0" y1="0" x2="100%" y2="100%" stroke="black" stroke-width="3" stroke-dasharray="4 14" stroke-linecap="round" />
                                    <?php else: ?>
                                        <line x1="100%" y1="0" x2="0" y2="100%" stroke="black" stroke-width="3" stroke-dasharray="4 14" stroke-linecap="round" />
                                    <?php endif; ?>
                                </svg>
                            <?php endif; ?>

                            <div class="absolute top-0 left-8 <?= $circlePos ?> transform -translate-x-1/2 w-[44px] h-[44px] rounded-full flex items-center justify-center shadow-md transition-all duration-300 
                                        bg-[#bad973] border-[3px] border-transparent 
                                        group-hover:bg-[#d2ea9b] group-hover:border-[#465e10] z-20 cursor-default">
                                <span class="font-sans font-normal text-black group-hover:text-[#465e10] text-[28px] leading-none transition-colors mt-[2px]">
                                    <?= $index + 1 ?>
                                </span>
                            </div>

                            <div class="w-full flex items-center min-h-[44px] pl-[5rem] md:pl-0 <?= $textContainerClass ?>">
                                <span class="font-history-serif font-bold text-[18px] text-ink-900 group-hover:text-[#465e10] transition-colors duration-300 cursor-default">
                                    <?= htmlspecialchars($stop) ?>
                                </span>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ol>

                <?php if ($totalStops > 6): ?>
                <div class="mt-14 flex justify-center relative z-20" id="itinerary-btn-wrap">
                    <button type="button" onclick="showFullItinerary()"
                            class="inline-flex items-center justify-center w-[209px] h-[42px] bg-[#fdefc4] hover:bg-[#fce8a8] text-black font-medium text-[16px] rounded-full transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#465e10]">
                        View full itinerary
                        <svg class="w-4 h-4 ml-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                <script>
                function showFullItinerary() {
                    document.querySelectorAll('.extra-stop').forEach(el => el.classList.remove('hidden'));
                    document.getElementById('itinerary-btn-wrap').classList.add('hidden');
                }
                </script>
                <?php endif; ?>

            </div>
        </div>
    </div>

</section>