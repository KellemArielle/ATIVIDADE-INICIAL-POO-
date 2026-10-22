<?php
class EstoqueInsuficienteException extends Exception {}
class PedidoInvalidoException extends Exception {}
class PagamentoException extends Exception {}
interface PagamentoInterface {
    public function pagar(float $valor): bool;
    public function getNomeFormaPagamento(): string;
}
class PagamentoPix implements PagamentoInterface {
    public function pagar(float $valor): bool {
        return true; // Simulação de pagamento aprovado
    }
    public function getNomeFormaPagamento(): string {
        return "PIX";
    }
}
class PagamentoCartao implements PagamentoInterface {
    private string $numeroCartao;

    public function __construct(string $numeroCartao = "4000 0000 0000 0000") {
        $this->numeroCartao = $numeroCartao;
    }
    public function pagar(float $valor): bool {
        return true; // Simulação de pagamento aprovado
    }

    public function getNomeFormaPagamento(): string {
        return "Cartão de Crédito";
    }
}
class PagamentoBoleto implements PagamentoInterface {
    public function pagar(float $valor): bool {
        return true; // Simulação de pagamento aprovado
    }
    public function getNomeFormaPagamento(): string {
        return "Boleto Bancário";
    }
}
abstract class Produto {
    protected int $codigo;
    protected string $nome;
    protected float $preco;
    protected int $estoque;
    public function __construct(int $codigo, string $nome, float $preco, int $estoque) {
        if ($preco < 0) {
            throw new PedidoInvalidoException("Erro: O preço do produto não pode ser negativo.");
        }
        if ($estoque < 0) {
            throw new PedidoInvalidoException("Erro: O estoque do produto não pode ser negativo.");
        }
        $this->codigo = $codigo;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
    }
    public function getCodigo(): int { return $this->codigo; }
    public function getNome(): string { return $this->nome; }
    public function getPreco(): float { return $this->preco; }
    public function getEstoque(): int { return $this->estoque; }
    public function setPreco(float $preco): void {
        if ($preco < 0) {
            throw new PedidoInvalidoException("Erro: O preço não pode ser alterado para um valor negativo.");
        }
        $this->preco = $preco;
    }
    public function adicionarEstoque(int $quantidade): void {
        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade adicionada ao estoque deve ser maior que zero.");
        }
        $this->estoque += $quantidade;
    }
    public function removerEstoque(int $quantidade): void {
        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade removida do estoque deve ser maior que zero.");
        }
        if ($quantidade > $this->estoque) {
            throw new EstoqueInsuficienteException("Estoque insuficiente para '{$this->nome}'. Disponível: {$this->estoque}, Solicitado: {$quantidade}.");
        }
        $this->estoque -= $quantidade;
    }
    abstract public function calcularPrecoFinal(): float;
}
class ProdutoFisico extends Produto {
    private float $peso; 
    private float $largura;
    private float $altura;
    private float $profundidade;

    private const TAXA_FRETE_POR_KG = 15.00;

    public function __construct(
        int $codigo,
        string $nome,
        float $preco,
        int $estoque,
        float $peso,
        float $largura = 0,
        float $altura = 0,
        float $profundidade = 0
    ) {
        parent::__construct($codigo, $nome, $preco, $estoque);
        $this->peso = $peso;
        $this->largura = $largura;
        $this->altura = $altura;
        $this->profundidade = $profundidade;
    }
    public function getPeso(): float { return $this->peso; }

    public function calcularFrete(): float {
        return $this->peso * self::TAXA_FRETE_POR_KG;
    }
    public function calcularPrecoFinal(): float {
        return $this->preco + $this->calcularFrete();
    }
}
class ProdutoDigital extends Produto {
    private float $tamanhoArquivo;
    private string $formato;
    private string $linkDownload;
    public function __construct(
        int $codigo,
        string $nome,
        float $preco,
        int $estoque,
        float $tamanhoArquivo,
        string $formato,
        string $linkDownload = "https://downloads.loja.com/arquivo"
    ) {
        parent::__construct($codigo, $nome, $preco, $estoque);
        $this->tamanhoArquivo = $tamanhoArquivo;
        $this->formato = $formato;
        $this->linkDownload = $linkDownload;
    }
    public function calcularFrete(): float {
        return 0.0; 
    }
    public function calcularPrecoFinal(): float {
        return $this->preco;
    }
}
class Endereco {
    private string $logradouro;
    private string $numero;
    private string $complemento;
    private string $bairro;
    private string $cidade;
    private string $estado;
    private string $cep;
    public function __construct(
        string $logradouro,
        string $numero,
        string $complemento,
        string $bairro,
        string $cidade,
        string $estado,
        string $cep
    ) {
        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->cep = $cep;
    }
    public function getCidade(): string { return $this->cidade; }
    public function getEstado(): string { return $this->estado; }
}
class Cliente {
    private int $codigo;
    private string $nome;
    private string $cpf;
    private string $email;
    private string $telefone;
    private array $enderecos = [];
    public function __construct(
        int $codigo,
        string $nome,
        string $cpf,
        string $email,
        string $telefone,
        Endereco $enderecoInicial
    ) {
        $this->codigo = $codigo;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->adicionarEndereco($enderecoInicial);
    }
    public function getCodigo(): int { return $this->codigo; }
    public function getNome(): string { return $this->nome; }
    public function getCpf(): string { return $this->cpf; }
    public function getEmail(): string { return $this->email; }
    public function adicionarEndereco(Endereco $endereco): void {
        $this->enderecos[] = $endereco;
    }
}
class ItemPedido {
    private Produto $produto;
    private int $quantidade;
    private float $precoUnitario;
    private float $subtotal;
    public function __construct(Produto $produto, int $quantidade) {
        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade do item deve ser maior que zero.");
        }
        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->precoUnitario = $produto->getPreco();
        $this->subtotal = $this->precoUnitario * $this->quantidade;
    }
    public function getProduto(): Produto { return $this->produto; }
    public function getQuantidade(): int { return $this->quantidade; }
    public function getPrecoUnitario(): float { return $this->precoUnitario; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getFreteTotal(): float {
        if ($this->produto instanceof ProdutoFisico) {
            return $this->produto->calcularFrete() * $this->quantidade;
        }
        return 0.0;
    }
}
class Pedido {
    private int $numero;
    private Cliente $cliente;
    private DateTime $data;
    private array $itens = [];
    private string $status;
    private float $valorTotal = 0.0;
    private float $valorFrete = 0.0;

    public function __construct(int $numero, Cliente $cliente) {
        $this->numero = $numero;
        $this->cliente = $cliente;
        $this->data = new DateTime();
        $this->status = 'Aberto';
    }

    public function getNumero(): int { return $this->numero; }
    public function getCliente(): Cliente { return $this->cliente; }
    public function getItens(): array { return $this->itens; }
    public function getStatus(): string { return $this->status; }
    public function getValorTotal(): float { return $this->valorTotal; }
    public function getValorFrete(): float { return $this->valorFrete; }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function adicionarItem(ItemPedido $item): void {
        if (in_array($this->status, ['Concluido', 'Cancelado'])) {
            throw new PedidoInvalidoException("Não é possível adicionar novos itens a um pedido com status '{$this->status}'.");
        }
        $this->itens[] = $item;
        $this->calcularValorTotal();
    }

    public function calcularValorTotal(): float {
        $subtotalItens = 0.0;
        $this->valorFrete = 0.0;

        foreach ($this->itens as $item) {
            $subtotalItens += $item->getSubtotal();
            $this->valorFrete += $item->getFreteTotal();
        }

        $this->valorTotal = $subtotalItens + $this->valorFrete;
        return $this->valorTotal;
    }
}


class PedidoService {

    public function validarPedido(Pedido $pedido): void {
        if (empty($pedido->getItens())) {
            throw new PedidoInvalidoException("O pedido deve possuir pelo menos um item antes de ser processado.");
        }
        if ($pedido->getStatus() === 'Cancelado') {
            throw new PedidoInvalidoException("Um pedido cancelado não pode ser pago.");
        }
        if ($pedido->getStatus() === 'Concluido') {
            throw new PedidoInvalidoException("Este pedido já foi concluído anteriormente.");
        }
    }

    public function verificarEstoque(Pedido $pedido): void {
        foreach ($pedido->getItens() as $item) {
            $produto = $item->getProduto();
            if ($item->getQuantidade() > $produto->getEstoque()) {
                throw new EstoqueInsuficienteException(
                    "Não é possível vender '{$produto->getNome()}' em quantidade superior ao estoque. Disponível: {$produto->getEstoque()}, Solicitado: {$item->getQuantidade()}."
                );
            }
        }
    }
    public function processarPedido(Pedido $pedido, PagamentoInterface $formaPagamento): bool {
        $this->validarPedido($pedido);
        $this->verificarEstoque($pedido);

        $pedido->setStatus('Aguardando Pagamento');

        $sucesso = $formaPagamento->pagar($pedido->getValorTotal());

        if (!$sucesso) {
            throw new PagamentoException("Falha no pagamento via " . $formaPagamento->getNomeFormaPagamento());
        }
        foreach ($pedido->getItens() as $item) {
            $item->getProduto()->removerEstoque($item->getQuantidade());
        }
        $pedido->setStatus('Pago');
        return true;
    }
    public function exibirResumo(Pedido $pedido, PagamentoInterface $formaPagamento): void {
        echo "\n";
        echo "PEDIDO Nº " . $pedido->getNumero() . "\n";
        echo "\n";
        echo "Cliente: " . $pedido->getCliente()->getNome() . "\n";
        echo "ITENS\n";

        foreach ($pedido->getItens() as $item) {
            echo "Produto: " . $item->getProduto()->getNome() . "\n";
            echo "Quantidade: " . $item->getQuantidade() . "\n";
            echo "Valor unitário: R$ " . number_format($item->getPrecoUnitario(), 2, ',', '.') . "\n";
            echo "Subtotal: R$ " . number_format($item->getSubtotal(), 2, ',', '.') . "\n";
        }

        echo "Frete: R$ " . number_format($pedido->getValorFrete(), 2, ',', '.') . "\n";
        echo "Valor Total: R$ " . number_format($pedido->getValorTotal(), 2, ',', '.') . "\n";
        echo "Forma de pagamento: " . $formaPagamento->getNomeFormaPagamento() . "\n";
        echo "Status: " . $pedido->getStatus() . "\n";
        echo "Pagamento realizado com sucesso.\n";
        echo "\n\n";
    }
}
echo "<pre style='font-family: Consolas, monospace; font-size: 14px; line-height: 1.5; padding: 15px;'>";
echo "\n";
echo "    SIMULAÇÃO DO CENÁRIO OBRIGATÓRIO (PEDIDO 1001)\n";
echo "\n";
try {
    $endereco = new Endereco("Rua das Flores", "123", "Apto 101", "Centro", "Belo Horizonte", "MG", "30100-000");
    $cliente = new Cliente(1, "Carlos Silva", "123.456.789-00", "carlos@email.com", "(31) 99999-8888", $endereco);
    $teclado = new ProdutoFisico(101, "Teclado Mecânico", 350.00, 10, 1.2);
    $curso = new ProdutoDigital(201, "Curso PHP Orientado a Objetos", 199.90, 100, 850.0, "MP4");
    $pedido = new Pedido(1001, $cliente);
    $pedido->adicionarItem(new ItemPedido($teclado, 2));
    $pedido->adicionarItem(new ItemPedido($curso, 1));
    $service = new PedidoService();
    $formaPagamento = new PagamentoPix();
    $service->processarPedido($pedido, $formaPagamento);
    $service->exibirResumo($pedido, $formaPagamento);
} catch (Exception $e) {
    echo "Erro na simulação do cenário principal: " . $e->getMessage() . "\n";
}
echo "</pre>";