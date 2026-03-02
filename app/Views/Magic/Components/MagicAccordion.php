<?php
namespace App\Views\Magic\Components;
use App\CmsModels\PageSection;
/** @var PageSection $section */

?>

<section class="w-[70%] flex flex-col gap-5">
    <?php if (!empty($section) && !empty($section->content_html_2)): ?>
    <?php echo $section->content_html_2; ?>
    <?php else: ?>
    <header class="text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)] font-bold">
        <strong>
            <h2>Arriving by Car or Public Transport</h2>
        </strong>
    </header>
    <?php endif; ?>
    <div class="w-[70%] accordion-group" data-accordion-root>
        <?php if (!empty($section) && !empty($section->content_html)): ?>
        <?php echo $section->content_html; ?>
        <?php else: ?>


        <!-- <div class="accordion-item"><button
                class="accordion-header w-full px-6 py-5 border-none text-left cursor-pointer flex justify-between items-center transition-colors duration-200"
                data-accordion-trigger="true" type="button" aria-expanded="false">
                <span class="accordion-title text-[#d4af37] font-courierprime tracking-widest text-2xl font-medium">Arriving
                    by Public Transport</span> <span
                    class="accordion-toggle text-[#d4af37] text-3xl font-light select-none">+</span> </button>
            <div class="accordion-content w-full overflow-hidden transition-all duration-300 block">
                <div class="accordion-body px-6 py-5 text-white font-robotomono leading-relaxed text-lg">
                    <p>Teylers Museum is easy to get to from the Haarlem railway station (ca 13-minute walk). How to get to
                        Teylers Museum from the railway station on foot: Exit the station onto Stationsplein. Turn left,
                        immediately upon leaving the station, to reach Jansweg. Turn right into Jansweg. Follow Jansweg
                        across the canal Nieuwe Gracht, where it becomes Jansstraat. Turn left into Korte Jansstraat, the
                        first street on your left after crossing the canal Nieuwe Gracht. Turn right onto Bakenessergracht,
                        or Bakenesser canal, and follow it to its end, where it meets the river Spaarne. Turn right along
                        the Spaarne. You will find Teylers Museum almost immediately on your right. The museum cannot easily
                        be reached by bus.</p>
                </div>
            </div>
        </div> -->

        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    if (window.magicAccordionBound) {
        return;
    }
    window.magicAccordionBound = true;

    const isContentOpen = (content) => {
        if (!content) {
            return false;
        }

        if (content.classList.contains('hidden')) {
            return false;
        }

        if (content.style.display === 'none') {
            return false;
        }

        return window.getComputedStyle(content).display !== 'none';
    };

    const findTrigger = (item) => item.querySelector('[data-accordion-trigger], .accordion-header');

    const closeContent = (content) => {
        if (!content) {
            return;
        }
        content.classList.add('hidden');
        content.style.display = 'none';
    };

    const openContent = (content) => {
        if (!content) {
            return;
        }
        content.classList.remove('hidden');
        content.style.display = '';
    };

    const syncState = (root) => {
        root.querySelectorAll('.accordion-item').forEach((item) => {
            const trigger = findTrigger(item);
            const content = item.querySelector('.accordion-content');
            const toggle = item.querySelector('.accordion-toggle');

            if (!trigger || !content || !toggle) {
                return;
            }

            const open = isContentOpen(content);
            toggle.textContent = open ? '−' : '+';
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    };

    const collapseAllOnInit = (root) => {
        root.querySelectorAll('.accordion-item').forEach((item) => {
            const trigger = findTrigger(item);
            const content = item.querySelector('.accordion-content');
            const toggle = item.querySelector('.accordion-toggle');

            closeContent(content);

            if (toggle) {
                toggle.textContent = '+';
            }

            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    };

    document.querySelectorAll('[data-accordion-root], .accordion-group').forEach((root) => {
        collapseAllOnInit(root);
        syncState(root);
    });

    document.addEventListener('click', function(event) {
        const trigger = event.target.closest('[data-accordion-trigger], .accordion-header');
        if (!trigger) {
            return;
        }

        const item = trigger.closest('.accordion-item');
        const root = trigger.closest('[data-accordion-root], .accordion-group');
        if (!item || !root) {
            return;
        }

        const content = item.querySelector('.accordion-content');
        const toggle = trigger.querySelector('.accordion-toggle');
        if (!content || !toggle) {
            return;
        }

        const isOpen = isContentOpen(content);

        root.querySelectorAll('.accordion-item').forEach((otherItem) => {
            if (otherItem === item) {
                return;
            }

            const otherContent = otherItem.querySelector('.accordion-content');
            const otherTrigger = findTrigger(otherItem);
            const otherToggle = otherItem.querySelector('.accordion-toggle');

            closeContent(otherContent);
            if (otherToggle) {
                otherToggle.textContent = '+';
            }
            if (otherTrigger) {
                otherTrigger.setAttribute('aria-expanded', 'false');
            }
        });

        if (isOpen) {
            closeContent(content);
            toggle.textContent = '+';
            trigger.setAttribute('aria-expanded', 'false');
        } else {
            openContent(content);
            toggle.textContent = '−';
            trigger.setAttribute('aria-expanded', 'true');
        }
    });
})();
</script>