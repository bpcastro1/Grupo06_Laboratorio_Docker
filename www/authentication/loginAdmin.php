<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veris Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="rounded-lg border max-w-md mx-auto my-10 shadow-md bg-gray-800 text-white" data-v0-t="card">
  <form action="loginA.php" method="post">
    <div class="flex flex-col space-y-1.5 p-6">
      <h3 class="tracking-tight text-2xl text-center font-semibold">Administrador</h3>
    </div>
    <div class="p-6 space-y-4">
      <div class="space-y-2">
        <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-semibold" for="email">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="w-4 h-4 mr-1 inline"
          >
            <path d="M22 17a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.5C2 7 4 5 6.5 5H18c2.2 0 4 1.8 4 4v8Z"></path>
            <polyline points="15,9 18,9 18,11"></polyline>
            <path d="M6.5 5C9 5 11 7 11 9.5V17a2 2 0 0 1-2 2v0"></path>
            <line x1="6" x2="7" y1="10" y2="10"></line>
          </svg>
          Usuario
        </label>
        <input
          class="flex h-10 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 p-2 w-full bg-gray-700 border border-gray-600 rounded-md text-white focus:border-blue-400 focus:outline-none"
          id="usuario"
          name="usuario"
          required=""
          type="text"
          value="ADM"
          disabled
        />
      </div>
      <div class="space-y-2">
        <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-semibold" for="password">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="w-4 h-4 mr-1 inline"
          >
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          Password
        </label>
        <input
          class="flex h-10 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 p-2 w-full bg-gray-700 border border-gray-600 rounded-md text-white focus:border-blue-400 focus:outline-none"
          id="password"
          name="password"
          required=""
          type="password"
        />
      </div>
      <div class="flex items-center">
        <input
          class="flex rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mr-2 h-4 w-4 text-blue-500"
          id="2fa"
          name="2fa"
          type="checkbox"
        />
        <label
          class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-semibold inline-flex items-center"
          for="2fa"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="w-4 h-4 mr-1"
          >
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
          </svg>
          Habilitar 2FA
        </label>
      </div>
    </div>
    <div class="p-6 flex flex-col items-center space-y-4">
      
      <?php if (isset($_GET['error'])) : ?>
        <p class="text-red-500"><?php echo $_GET['error']; ?></p>
      <?php endif; ?>
      <button type="submit" class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-8 py-2 bg-blue-500 text-white w-full rounded-md hover:bg-blue-600">
        Iniciar Sesión
      </button>
      
    </div>
  </form>
</div>
</body>
</html>
