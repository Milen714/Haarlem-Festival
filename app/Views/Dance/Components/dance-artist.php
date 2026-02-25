<?php 
namespace App\Views\Dance\Components;
/** @var object|null $artists */
?>
<section class="bg-[#0D0D0D] py-20 px-4">
    <div class="max-w-5xl mx-auto text-center">
        
        <h2 class="inline-block text-[#f08a8a] text-xl font-bold uppercase tracking-[0.2em] border-b-2 border-[#f08a8a] mb-6 pb-1">
            Dutch Dance Legends
        </h2>
        
        <p class="text-xs md:text-sm leading-relaxed mb-12 px-4 max-w-2xl mx-auto text-white">
            The Netherlands are the birthplace of trance and techno music. We are proud to bring 
            the biggest Dutch dance sensations. Join us to see the local legends...
        </p>
        
       <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-3 gap-4 max-w-4xl mx-auto auto-rows-fr">
            <?php foreach ($artists as $artist): ?>
                <a href="/dance/artist/<?= $artist->slug ?>" class="flex flex-col items-center group cursor-pointer h-full">
                    
                    <div class="relative w-full aspect-square overflow-hidden bg-gray-900">
                        <img src="<?= $artist->file_path ?>" 
                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition duration-500 ease-in-out" 
                             alt="<?= $artist->name ?>">
                    </div>
                    
                    <div class="py-4 w-full text-center bg-[#0D0D0D]">
                        <h3 class="text-[10px] md:text-xs font-semibold uppercase tracking-[0.2em] text-white">
                            <?= $artist->name ?>
                        </h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>