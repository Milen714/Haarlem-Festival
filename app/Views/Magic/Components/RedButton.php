<?php
namespace App\Views\Magic\Components;
?>

<a class="relative rounded-[7.5px] bg-[#902106] border-[3px] border-[#faa505] box-border w-full flex items-center justify-center p-[2.3px] text-center text-[20px] text-[#faa505] [font-family:Roboto] w-[50%] mt-4 hover:bg-[#902106] hover:border-[#faa505] focus:outline-none focus:ring-2 focus:ring-[#faa505] focus:ring-offset-2 transition-colors duration-200"
    href="<?= htmlspecialchars($ctaURL) ?>">
    <div
        class="rounded-[5px] border-[2.5px] border-[rgba(0,0,0,0.35)] flex flex-col items-center justify-center py-[18px] px-[32px] w-full">
        <b class="relative"><?php echo htmlspecialchars($buttonLabel); ?></b>
    </div>
</a>