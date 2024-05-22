<script>
    import axios from 'axios';

    let email = '';
    let password = '';
    let errorMessage = '';

    async function loginUser() {
        try {
            const response = await axios.post('http://localhost:5173/login', { email, password });
            // Handle successful login (e.g., redirect to dashboard)
            console.log('Login successful');
        } catch (error) {
            errorMessage = error.response ? error.response.data.error : 'An error occurred during login';
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl mb-4">Login</h2>
        <form on:submit|preventDefault="{loginUser}">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email" bind:value={email}>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="*********" bind:value={password}>
            </div>
            {#if errorMessage}
                <p class="text-red-500 text-xs italic">{errorMessage}</p>
            {/if}
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Sign In
                </button>
                <a href="/register" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Register</a>
            </div>
        </form>
    </div>
</div>
