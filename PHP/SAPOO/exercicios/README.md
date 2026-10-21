
1. **Falta de Encapsulamento:** Arrays não protegem dados contra alterações indevidas. **Duplicação de Lógica:** Regras de negócio ficam espalhadas por vários scripts. **Falta de Contratos (Tipagem Fraca):** Arrays não garantem a existência de chaves necessárias para a operação.

2. O PHP aloca espaço na memória Heap para a nova instância. O método `__construct()` é chamado imediatamente a seguir para inicializar o estado do objeto (atribuir dependências e valores iniciais), garantindo que ele nasça válido e pronto para uso.

3. O `$this` é uma referência à instância atual. `$this->saldo` aponta para a propriedade do objeto armazenado na memória, enquanto `$saldo` é apenas uma variável local temporária que deixa de existir ao final da execução do método.

4. Porque destrói as invariantes do domínio. Um saldo bancário não deve ser sobrescrito com um valor arbitrário, mas sim ser o resultado de operações de negócio (como depósitos e saques) que passam por regras de validação, limites e geram histórico.

5. Propriedades e métodos `private` só podem ser acessados pela própria classe onde foram declarados. Já os `protected` mantêm o encapsulamento externo, mas permitem que as subclasses (classes filhas) acessem e alterem seus valores diretamente.

6. Ele permite declarar a propriedade, definir sua visibilidade e atribuir o valor recebido tudo na própria assinatura do construtor (ex: `public function __construct(private string $cpf) {}`), eliminando linhas de declaração e atribuição manuais.

7. Garante a imutabilidade do objeto após sua inicialização. Propriedades `readonly` não podem ser alteradas depois de definidas no construtor. Isso é essencial para Objetos de Valor (Value Objects) como CPF, evitando efeitos colaterais e garantindo a integridade dos dados.

8. Evita a instanciação de uma conta genérica inválida (`new ContaBancaria()`), pois no mundo real apenas produtos específicos existem. Ao mesmo tempo, permite concentrar todo o código comum (saldo, titular) na classe pai para ser herdado pelas classes concretas.

9. Através do Polimorfismo e Type Hinting. Como o parâmetro exige o tipo pai `ContaBancaria`, o PHP aceita qualquer classe filha. O método tem a garantia de que os métodos exigidos pelo contrato (como `depositar`) existem, independentemente da implementação interna específica de cada classe filha.

10. Tipos de retorno primitivos (`false` ou `null`) falham silenciosamente, não explicam o motivo do erro e o programador pode esquecer de validá-los. Lançar uma exceção de domínio (como `SaldoInsuficienteException`) interrompe o fluxo, obriga o tratamento via `try/catch` e deixa claro exatamente o que deu errado.