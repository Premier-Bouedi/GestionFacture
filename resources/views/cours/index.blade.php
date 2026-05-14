<x-app-layout>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="mb-0">Mes cours</h4>
            </div>

            <div class="card-body">
                <p>Bienvenue, {{ Auth::user()->name }}</p>
                <p>Email : {{ Auth::user()->email }}</p>

                <ul class="list-group">
                    <li class="list-group-item">Laravel</li>
                    <li class="list-group-item">UML</li>
                    <li class="list-group-item">JavaScript</li>
                </ul>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
