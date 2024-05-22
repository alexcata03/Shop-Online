<script>
    import axios from 'axios';

    let email = '';
    let password = '';
    let firstName = '';
    let lastName = '';
    let errorMessage = '';

    async function registerUser() {
        try {
            const response = await axios.post('http://localhost:8000/register', { 
                email, 
                password, 
                firstName,
                lastName
            });
            // Handle successful registration (e.g., redirect to login page)
            console.log('Registration successful');
        } catch (error) {
            errorMessage = error.response.data.error || 'An error occurred during registration';
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl mb-4">Register</h2>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email" bind:value={email}>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="*********" bind:value={password}>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="firstName">
                First Name
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="firstName" type="text" placeholder="First Name" bind:value={firstName}>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="lastName">
                Last Name
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="lastName" type="text" placeholder="Last Name" bind:value={lastName}>
        </div>
        {#if errorMessage}
            <p class="text-red-500 text-xs italic">{errorMessage}</p>
        {/if}
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" on:click={registerUser}>
                Register
            </button>
            <!-- Log In button with anchor tag -->
            <a href="/" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Log In</a>
        </div>
    </div>
</div>
