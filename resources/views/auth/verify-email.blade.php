<x-app-layout>
    <main>
        <div class="container">
            <div class="card p-large my-large">
                <h2>Verify Your Email Address</h2>
                <div class="my-medium">
                    Before processing, please check you email for verification link.
                    If you didn't receive email with a link,
                    <form action="{{ route('verification.send') }}" method="POST" class="inline-flex">
                        @csrf
                        <button class="btn-link">Please click this link to receive new one</button>
                    </form>
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
