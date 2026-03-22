<?php
use App\ViewModels\ShoppingCart\OrderItemViewModel;
use App\Models\Payment\OrderItem;

/** @var OrderItem $item */
$p = new OrderItemViewModel($item);
$cardStyles = $p->getCardStyles();
$cardImage  = $p->getCardImage();
?>

<article class="flex flex-row w-full rounded-lg overflow-hidden shadow-md border border-gray-200">
    <div class="relative calendar-coils w-2 md:w-[0.65rem] <?= $cardStyles['side'] ?> text-transparent flex-shrink-0">
        hh
        <div class="absolute w-[1rem] h-[1rem] -bottom-[-2rem] -left-2.5 bg_colors_home rounded-full ">
        </div>
    </div>

    <img loading="lazy" src="<?= htmlspecialchars($cardImage->file_path ?? '') ?>"
        alt="<?= htmlspecialchars($cardImage->alt_text ?? '') ?>"
        class="w-16 sm:w-20 md:w-30 lg:w-40 h-auto object-cover flex-shrink-0">
    <?php if (isset($showCrudButtons) && $showCrudButtons === true): ?>
    <ol class="flex flex-col items-center justify-center">
        <li class="flex-grow flex items-center px-2 bg-[--home-dance-accent]">
            <button class="edit-order-item"
                data-orderItemSessionId="<?= htmlspecialchars($item->sessionOrderitem_id) ?>">
                <img src="/Assets/Home/EditIcon.svg" alt="Edit Item Icon">
            </button>
        </li>
        <li class="flex-grow flex items-center px-2 bg-red-400">
            <button class="delete-order-item"
                data-orderItemSessionId="<?= htmlspecialchars($item->sessionOrderitem_id) ?>">
                <img src="/Assets/Home/DeleteIcon.svg" alt="Delete Item Icon">
            </button>
        </li>
    </ol>
    <?php endif; ?>

    <div class="flex flex-col flex-grow min-w-0">
        <div class="flex flex-col py-1">
            <div class="flex flex-col lg:flex-row justify-between gap-3 mt-2">
                <div class="flex pl-2 gap-2 items-center">
                    <div class="h-min px-2 md:px-4 py-2 md:py-4 <?= $cardStyles['muted'] ?> rounded-md flex-shrink-0">
                        <h1 class="font-bold text-lg md:text-2xl text-black text-center">
                            <?= htmlspecialchars($p->getDateBoxLabel()) ?>
                        </h1>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-black text-sm md:text-lg font-semibold">
                            <?= htmlspecialchars($p->getDisplayName()) ?>
                        </h2>
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span class="text-sm text-black">
                                    <?= htmlspecialchars($p->getVenueDisplay()) ?>
                                </span>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/DurationIconHome.svg" alt="Duration Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span class="text-sm font-bold text-black whitespace-nowrap">
                                    <?= htmlspecialchars($p->getDurationDisplay()) ?>
                                </span>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/PersonIcon.svg" alt="Quantity Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span class="text-sm font-bold text-black whitespace-nowrap">
                                    x <?= (int)$item->quantity ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <article class="flex-shrink-0 flex items-center mr-3 justify-center">
                    <header class="text-black">
                        <h3 class="font-semibold text-xs md:text-base mb-2">
                            €<?= number_format($item->subtotal, 2) ?>
                        </h3>
                    </header>
                </article>
            </div>
        </div>
    </div>
</article>