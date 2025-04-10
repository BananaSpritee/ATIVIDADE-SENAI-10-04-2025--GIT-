<?php

// Caminho para o arquivo de tarefas

$tarefasFile= 'tarefas.json';

// Função para ler as tarefas de JSON

function lerTarefas() {
    global $tarefasFile;

    if(file_exists($tarefasFile)) {

        // Lê o conteudo do arquivo

        $conteudo = file_get_contents($tarefasFile);

        // Converte o conteúdo JSON em um array

        return json_decode($conteudo, true);

    }
    return ['Arquivo inexistente']; // Se o arquivo não existir
}

// Função para salvar as tarefas no arquivo JSON

function salvarTarefas($tarefas) {
    global $tarefasFile;

    // Converte o array de tarefas para o formato JSON

    $conteudoJson = json_encode($tarefas, JSON_PRETTY_PRINT);

    // Salva o conteúdo JSON no arquivo

    file_put_contents($tarefasFile, $conteudoJson);
}

// Verifica o método de requisição

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'GET') {
    
    // Se for GET, retorna as tarefas

    $tarefas = lerTarefas();
    header('Content-Type: application/json');
    echo json_encode($tarefas);
} elseif ($metodo == 'POST') {

    // Se for POST, adiciona uma nova tarefa

    $dadosPost = json_decode(file_get_contents('php://input'), true);

    // Verifica se o campo 'titulo' está presente

    if(isset($dadosPost['titulo'])) {
        $novaTarefa = [
            'titulo' => $dadosPost['titulo'],
            'id' => uniqid() // Gerando um ID único para a tarefa
        ];

        // Lê as tarefas existentes

        $tarefas = lerTarefas();

        // Adiciona a nova tarefa

        $tarefas[] = $novaTarefa;

        // Salva as tarefas novamente no arquivo

        salvarTarefas($tarefas);

        // Retorna a nova tarefa como resposta

        header('Content-Type: application/json');
        echo json_encode($novaTarefa);
    } else {
        
        // Caso o título não seja fornecido, retorna erro

        http_response_code(400);
        echo json_encode(['erro' => 'O campo titulo é obrigatório.']);
    }
} else {
    
    // Se for outro método, retorna o erro 405 (Method Not Allowed)

    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
}