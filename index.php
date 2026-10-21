<?php
// A tag <pre> força o navegador a respeitar todas as quebras de linha (\n) e espaços do PHP
echo "<pre style='font-family: monospace; font-size: 16px; line-height: 1.5; background-color: #f8f9fa; padding: 20px; border-radius: 8px; color: #212529;'>";

class Produto 
{
    public string $nome;
    public float $preco;
    public int $quantidade;

    public function __construct(string $nome, float $preco, int $quantidade) 
    {
        $this->nome = $nome;
        $this->preco = $preco;
        $this->quantidade = $quantidade;
    }

    public function exibirDados(): void 
    {
        echo "Produto: " . $this->nome . "\n";
        echo "Preço: R$ " . number_format($this->preco, 2, ',', '.') . "\n";
        echo "Quantidade: " . $this->quantidade . "\n";
    }

    public function calcularValorEstoque(): float 
    {
        return $this->preco * $this->quantidade;
    }

    public function adicionarEstoque(int $quantidade): void 
    {
        $this->quantidade += $quantidade;
    }

    public function retirarEstoque(int $quantidade): void 
    {
        if ($quantidade <= $this->quantidade) {
            $this->quantidade -= $quantidade;
        } else {
            echo "Quantidade insuficiente em estoque.\n";
        }
    }
}

echo "\n";
echo "CONTROLE DE ESTOQUE:\n";
echo "\n\n";

$produto1 = new Produto("Teclado", 150.00, 4);
$produto2 = new Produto("Mouse", 80.00, 10);
$produto3 = new Produto("Monitor", 900.00, 3);

// Exibindo dados e valor em estoque do Produto 1
$produto1->exibirDados();
echo "Valor em estoque: R$ " . number_format($produto1->calcularValorEstoque(), 2, ',', '.') . "\n";
echo "\n";

// Exibindo dados e valor em estoque do Produto 2
$produto2->exibirDados();
echo "Valor em estoque: R$ " . number_format($produto2->calcularValorEstoque(), 2, ',', '.') . "\n";
echo "\n";

// Exibindo dados e valor em estoque do Produto 3
$produto3->exibirDados();
echo "Valor em estoque: R$ " . number_format($produto3->calcularValorEstoque(), 2, ',', '.') . "\n";
echo "\n";

// --- TESTE DOS DESAFIOS (Movimentação de Estoque) ---
echo "\nMovimentação de Estoque:\n";

echo "\nAdicionando 3 unidades ao Teclado...\n";
$produto1->adicionarEstoque(3);
echo "Nova quantidade de Teclados: " . $produto1->quantidade . "\n";
echo "Novo valor em estoque: R$ " . number_format($produto1->calcularValorEstoque(), 2, ',', '.') . "\n";

echo "\nTentando retirar 15 unidades do Mouse...\n";
$produto2->retirarEstoque(15);

echo "</pre>";
?>