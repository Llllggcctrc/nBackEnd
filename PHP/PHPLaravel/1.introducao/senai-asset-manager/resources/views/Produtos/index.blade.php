<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Asset Manager - Produtos</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📦 Gestão de Produtos e Insumos</h2>
            <a href="{{ route('produtos.create') }}" class="btn btn-primary">+ Cadastrar Produto</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Fabricante</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Total em Estoque</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produtos as $produto)
                            <tr>
                                <td>{{ $produto->id }}</td>
                                <td><strong>{{ $produto->nome }}</strong></td>
                                <td>{{ $produto->fabricante }}</td>
                                <td>{{ $produto->quantidade }} un.</td>
                                <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($produto->quantidade * $produto->preco, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-sm btn-info text-white">Ficha</a>
                                    <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                    
                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este produto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Nenhum produto cadastrado no momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
