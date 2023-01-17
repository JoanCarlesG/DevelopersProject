<header class="bg-white shadow">
    <div class="w-full max-w-2xl mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
        <?php if (str_starts_with($_SERVER['REQUEST_URI'],WEB_ROOT . '/home')) { ?>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900"><i class="fa-solid fa-house mx-4"></i>YOUR TASKS
            </h1>
        <?php } else if (str_starts_with($_SERVER['REQUEST_URI'],WEB_ROOT . '/update_task')){
            ?>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900"><i class="fa-solid fa-pencil mx-4"></i>TASK EDITOR
            </h1>
        <?php } ?>
    </div>
</header>