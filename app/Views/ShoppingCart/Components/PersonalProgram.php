<?php
namespace App\Views\ShoppingCart\Components;
use App\Models\Enums\EventType;
use App\Models\Media;
?>
<main class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-[#1e4b6e]">My Personal Program</h1>
            <p class="text-sm text-gray-500 mt-1">Here are all the tickets and reservations you have successfully purchased.</p>
        </header>

        <section class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                
                <?php if (empty($tickets)): ?>
                    <div class="text-center py-12">
                        <span class="text-4xl">🎟️</span>
                        <h3 class="text-lg font-bold text-gray-700 mt-4">Your program is empty</h3>
                    </div>
                <?php else: ?>

                    <?php 
                    /** @var \App\Models\Payment\OrderItem $item */
                    foreach($tickets as $item): 
                        
                        // LÓGICA IDÉNTICA A LA DE TU CARRITO (TicketItemRow.php)
                        $scheduleRef = $item->ticket_type->schedule; 
                        $eventType = $scheduleRef->event_category?->type ?? null;
                        
                        $cardStyles = ['side' => 'bg-gray-400', 'text' => 'text-gray-800'];
                        $eventLabel = $scheduleRef->event_category?->title ?? 'Event';
                        $eventName = $item->ticket_type->ticket_scheme->name ?? 'Ticket';
                        $cardImage = new Media();
                        $cardImage->file_path = '/Assets/Home/ImagePlaceholder.png';

                        // Colores e imágenes dinámicas según el evento
                        switch ($eventType) {
                            case EventType::Jazz:
                                $cardStyles = ['side' => 'bg-[var(--home-jazz-accent)]'];
                                $cardImage = $scheduleRef->artist?->profile_image ?? $cardImage;
                                $eventName = $scheduleRef->artist?->name . ' - ' . $eventName;
                                break;
                            case EventType::Dance:
                                $cardStyles = ['side' => 'bg-[var(--home-dance-accent)]'];
                                $cardImage = $scheduleRef->artist?->profile_image ?? $cardImage;
                                $eventName = $scheduleRef->artist?->name . ' - ' . $eventName;
                                break;
                            case EventType::Yummy:
                                $cardStyles = ['side' => 'bg-[var(--home-yummy-accent)]'];
                                $cardImage = $scheduleRef->restaurant?->main_image ?? $cardImage;
                                $eventName = $scheduleRef->restaurant?->name . ' - ' . $eventName;
                                break;
                            case EventType::History:
                                $cardStyles = ['side' => 'bg-[var(--home-history-accent)]'];
                                $cardImage = $scheduleRef->landmark?->main_image_id ?? $cardImage;
                                $eventName = $scheduleRef->landmark?->name ?? 'History Tour';
                                break;
                            case EventType::Magic:
                                $cardStyles = ['side' => 'bg-[var(--home-magic-accent)]'];
                                $eventName = 'Magic: ' . $eventName;
                                break;
                        }
                    ?>
                        <article class="relative flex flex-col sm:flex-row border border-gray-200 rounded-md overflow-hidden bg-white mb-6 hover:shadow-md transition-shadow">
                            
                            <div class="w-full sm:w-3 <?= $cardStyles['side'] ?>"></div> 
                            
                            <div class="p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">
                                        Haarlem <?= htmlspecialchars($eventLabel) ?>
                                    </span>
                                    <div class="flex justify-between items-start mt-1">
                                        <div>
                                            <h2 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($eventName) ?></h2>
                                            <p class="text-xs text-gray-500 mt-1">
                                                📅 <?= $scheduleRef->date?->format('l, d M Y') ?? 'TBA' ?> · 
                                                <?= $scheduleRef->start_time?->format('H:i') ?? 'TBA' ?> - 
                                                <?= $scheduleRef->end_time?->format('H:i') ?? 'TBA' ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                📍 <?= htmlspecialchars($scheduleRef->venue?->name ?? $scheduleRef->landmark?->name ?? 'Location TBA') ?>
                                            </p>
                                            <p class="text-sm font-semibold text-black mt-2">👤 <?= $item->quantity ?>x Tickets</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <footer class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <button class="text-xs text-blue-600 font-bold uppercase tracking-wide hover:underline">📥 Download PDF</button>
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Paid</span>
                                </footer>
                            </div>
                            
                            <figure class="w-full sm:w-48 bg-gray-100 flex-shrink-0">
                                <img src="<?= htmlspecialchars($cardImage->file_path) ?>" alt="Event Image" class="w-full h-full object-cover">
                            </figure>
                        </article>
                    <?php endforeach; ?>
                    
                <?php endif; ?>

            </div>
        </section>
    </div>
</main>