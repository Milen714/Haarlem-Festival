<?php
namespace App\Views\Home;


if (isset($param)) {
        echo htmlspecialchars($param);
    }
    
    $user = new \App\Models\User();
if (isset($userModel)) {
    $user = $userModel;
    echo $user->fname;
    echo $user->email;
}



?>

<h1 class="text-center m-5 font-serif">Signup Page</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form method='POST' action='/signup'>
        <article class="input_group">
            <label class="input_label" for="email">Email:</label>
            <input class="form_input" type="email" id="email" name="email"
                value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="password">Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="fname">First Name:</label>
            <input class="form_input" type="text" id="fname" name="fname"
                value="<?php echo htmlspecialchars($user->fname ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="lname">Last Name:</label>
            <input class="form_input" type="text" id="lname" name="lname"
                value="<?php echo htmlspecialchars($user->lname ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="address">Address:</label>
            <input class="form_input" type="text" id="address" name="address"
                value="<?php echo htmlspecialchars($user->address ?? ''); ?>">
        </article>
        <article class="input_group">
            <label class="input_label" for="phone">Phone:</label>
            <input class="form_input" type="tel" id="phone" name="phone"
                value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
        </article>
        <button class="button_primary" type="submit">Signup</button>
    </form>