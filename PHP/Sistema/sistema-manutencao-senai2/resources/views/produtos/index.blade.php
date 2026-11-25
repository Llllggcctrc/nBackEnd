<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto - SENAI Asset Manager</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Cadastrar Novo Produto</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('produtos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Sensor Indutivo M12" required>
                    </div>

                    <div class="mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" name="fabricante" id="fabricante" class="form-control" placeholder="Ex: Siemens" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantidade" class="form-label">Quantidade</label>
                            <input type="number" name="quantidade" id="quantidade" class="form-control" min="0" value="1" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço Unitário (R$)</label>
                            <input type="number" step="0.01" name="preco" id="preco" class="form-control" placeholder="0.00" min="0" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>
                        <button type="submit" class="btn btn-success">Salvar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
