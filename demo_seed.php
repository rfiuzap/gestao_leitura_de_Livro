<?php
declare(strict_types=1);

/*
 * Popula o ambiente DEMO com leitores, livros e resenhas ficticios e grava
 * data/demo_base.sqlite, a copia para a qual a demo volta a cada 2 horas.
 *
 * Uso (somente pelo terminal, com APP_ENV=demo no .env):
 *   php demo_seed.php
 *
 * ATENCAO: apaga todos os usuarios e livros do banco atual.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/config.php';

if (!is_demo()) {
    fwrite(STDERR, "Recusado: APP_ENV precisa ser 'demo' (atual: " . APP_ENV . ").\n");
    exit(1);
}

mt_srand(2026);

$readers = [
    ['Visitante Demo', DEMO_EMAIL, 'outro', 'Brasil', 'SP'],
    ['Ana Beatriz Lima', 'ana.lima@exemplo.com', 'feminino', 'Brasil', 'RJ'],
    ['Carlos Mendes', 'carlos.mendes@exemplo.com', 'masculino', 'Brasil', 'MG'],
    ['Juliana Rocha', 'juliana.rocha@exemplo.com', 'feminino', 'Brasil', 'RS'],
    ['Pedro Albuquerque', 'pedro.albuquerque@exemplo.com', 'masculino', 'Portugal', 'Lisboa'],
    ['Mariana Costa', 'mariana.costa@exemplo.com', 'feminino', 'Brasil', 'BA'],
];

// [titulo, autor, ano, paginas, genero, personagens, resumo]
$books = [
    ['Dom Casmurro', 'Machado de Assis', 1899, 256, 'Romance', ['Bentinho', 'Capitu', 'Escobar', 'José Dias'], 'Bentinho, já velho, narra a própria vida e o casamento com Capitu, tentando provar uma traição que o leitor nunca consegue confirmar.'],
    ['Memórias Póstumas de Brás Cubas', 'Machado de Assis', 1881, 208, 'Romance', ['Brás Cubas', 'Virgília', 'Quincas Borba'], 'Um defunto autor conta sua vida com ironia, expondo a vaidade e o egoísmo da elite do Rio de Janeiro do século XIX.'],
    ['Vidas Secas', 'Graciliano Ramos', 1938, 176, 'Romance', ['Fabiano', 'Sinhá Vitória', 'Baleia'], 'Uma família de retirantes atravessa o sertão fugindo da seca, em capítulos curtos que revelam a dureza da vida e a falta de voz dos personagens.'],
    ['O Cortiço', 'Aluísio Azevedo', 1890, 304, 'Romance', ['João Romão', 'Bertoleza', 'Rita Baiana'], 'A ascensão de um comerciante ambicioso e a vida coletiva de um cortiço carioca, retratados com o olhar naturalista.'],
    ['Grande Sertão: Veredas', 'João Guimarães Rosa', 1956, 624, 'Romance', ['Riobaldo', 'Diadorim', 'Hermógenes'], 'O ex-jagunço Riobaldo relembra suas andanças pelo sertão, o pacto com o diabo e o amor por Diadorim, numa linguagem reinventada.'],
    ['A Hora da Estrela', 'Clarice Lispector', 1977, 88, 'Romance', ['Macabéa', 'Rodrigo S.M.', 'Olímpico'], 'Um narrador conta a história de Macabéa, nordestina pobre no Rio de Janeiro, questionando o próprio ato de escrever.'],
    ['Capitães da Areia', 'Jorge Amado', 1937, 280, 'Romance', ['Pedro Bala', 'Professor', 'Sem-Pernas', 'Dora'], 'A vida de um grupo de meninos abandonados que vivem de pequenos furtos nas ruas de Salvador.'],
    ['O Alquimista', 'Paulo Coelho', 1988, 208, 'Ficção', ['Santiago', 'O Alquimista', 'Fátima'], 'Um jovem pastor andaluz viaja até as pirâmides do Egito em busca de um tesouro e aprende a seguir sua lenda pessoal.'],
    ['Torto Arado', 'Itamar Vieira Junior', 2019, 264, 'Romance', ['Bibiana', 'Belonísia', 'Zeca Chapéu Grande'], 'Duas irmãs do sertão baiano têm a vida marcada por um acidente na infância, numa história sobre terra, herança e resistência.'],
    ['1984', 'George Orwell', 1949, 416, 'Distopia', ['Winston Smith', 'Julia', "O'Brien"], 'Num Estado totalitário que vigia cada gesto, um funcionário do Partido começa a duvidar do sistema e a sonhar com liberdade.'],
    ['A Revolução dos Bichos', 'George Orwell', 1945, 152, 'Fábula', ['Napoleão', 'Bola de Neve', 'Sansão'], 'Os animais de uma fazenda expulsam o dono e criam sua própria sociedade, que aos poucos repete os abusos que combatia.'],
    ['O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 1943, 96, 'Infantojuvenil', ['O Pequeno Príncipe', 'O Aviador', 'A Raposa', 'A Rosa'], 'Um aviador perdido no deserto conhece um pequeno príncipe vindo de outro planeta e aprende sobre amizade e o que é essencial.'],
    ['Cem Anos de Solidão', 'Gabriel García Márquez', 1967, 448, 'Realismo mágico', ['José Arcadio Buendía', 'Úrsula Iguarán', 'Aureliano Buendía'], 'Sete gerações da família Buendía na cidade fictícia de Macondo, entre guerras, paixões e acontecimentos fantásticos.'],
    ['O Hobbit', 'J.R.R. Tolkien', 1937, 336, 'Fantasia', ['Bilbo Bolseiro', 'Gandalf', 'Thorin Escudo-de-Carvalho', 'Smaug'], 'Um hobbit caseiro é arrastado para uma aventura com anões e um mago para recuperar um tesouro guardado por um dragão.'],
    ['Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 1997, 264, 'Fantasia', ['Harry Potter', 'Hermione Granger', 'Rony Weasley', 'Alvo Dumbledore'], 'Um menino órfão descobre que é bruxo e começa seus estudos em Hogwarts, onde enfrenta um mistério ligado ao seu passado.'],
    ['Orgulho e Preconceito', 'Jane Austen', 1813, 424, 'Romance', ['Elizabeth Bennet', 'Sr. Darcy', 'Jane Bennet'], 'Elizabeth Bennet e o orgulhoso Sr. Darcy superam primeiras impressões e diferenças sociais na Inglaterra rural.'],
    ['O Diário de Anne Frank', 'Anne Frank', 1947, 352, 'Biografia', ['Anne Frank', 'Otto Frank', 'Peter van Pels'], 'O diário de uma adolescente judia escondida com a família em Amsterdã durante a ocupação nazista.'],
    ['Sapiens: Uma Breve História da Humanidade', 'Yuval Noah Harari', 2011, 472, 'História', [], 'Um panorama da trajetória do Homo sapiens, das revoluções cognitiva e agrícola até a era científica.'],
    ['O Poder do Hábito', 'Charles Duhigg', 2012, 408, 'Não ficção', [], 'Como os hábitos se formam no cérebro e como pessoas, empresas e sociedades podem mudá-los.'],
    ['Ensaio sobre a Cegueira', 'José Saramago', 1995, 312, 'Romance', ['A mulher do médico', 'O médico', 'O primeiro cego'], 'Uma epidemia de cegueira branca se espalha por uma cidade e revela o melhor e o pior do comportamento humano.'],
    ['A Menina que Roubava Livros', 'Markus Zusak', 2005, 480, 'Romance histórico', ['Liesel Meminger', 'Hans Hubermann', 'Max Vandenburg'], 'Narrada pela Morte, a história de uma menina que encontra refúgio nos livros na Alemanha nazista.'],
    ['O Senhor dos Anéis: A Sociedade do Anel', 'J.R.R. Tolkien', 1954, 576, 'Fantasia', ['Frodo Bolseiro', 'Gandalf', 'Aragorn', 'Samwise Gamgi'], 'Frodo parte numa jornada para destruir o Um Anel, acompanhado por uma sociedade de companheiros de diferentes povos.'],
    ['Quarto de Despejo', 'Carolina Maria de Jesus', 1960, 200, 'Biografia', ['Carolina Maria de Jesus'], 'O diário de uma catadora de papel da favela do Canindé, em São Paulo, registrando a fome e a luta diária pela sobrevivência.'],
    ['Extraordinário', 'R.J. Palacio', 2012, 320, 'Infantojuvenil', ['August Pullman', 'Via', 'Jack Will', 'Summer'], 'Auggie, um menino com uma deformidade facial, frequenta a escola pela primeira vez e ensina sobre empatia.'],
];

$reviews = [
    5 => ['Um dos melhores que já li. Recomendo muito!', 'Leitura marcante, daquelas que ficam com a gente.', 'Não consegui parar de ler. Obra-prima.'],
    4 => ['Muito bom, com alguns trechos mais lentos.', 'Gostei bastante da construção dos personagens.', 'Vale a leitura, final surpreendente.'],
    3 => ['Interessante, mas esperava mais.', 'Boa história, ritmo irregular.'],
    2 => ['Não me prendeu, mas entendo por que é um clássico.'],
];

function open_library_meta(string $title, string $author): array
{
    $url = 'https://openlibrary.org/search.json?limit=1&fields=cover_i&title=' . urlencode($title) . '&author=' . urlencode($author);
    $ctx = stream_context_create(['http' => ['timeout' => 8, 'header' => "User-Agent: LivroDemo/1.0\r\n"]]);
    $json = @file_get_contents($url, false, $ctx);
    $data = $json ? json_decode($json, true) : null;
    $cover = $data['docs'][0]['cover_i'] ?? null;

    return ['cover_url' => $cover ? "https://covers.openlibrary.org/b/id/{$cover}-L.jpg" : ''];
}

$pdo = db();
$pdo->exec('PRAGMA foreign_keys = OFF');
$pdo->beginTransaction();
foreach (['user_books', 'books', 'password_resets', 'rate_limit_attempts', 'users'] as $table) {
    $pdo->exec("DELETE FROM {$table}");
}
$pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('user_books','books','users','password_resets','rate_limit_attempts')");

$userIds = [];
$insertUser = $pdo->prepare('INSERT INTO users (name, email, password_hash, gender, country, state, is_profile_public, email_verified_at, security_question, security_answer_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)');
foreach ($readers as $i => [$name, $email, $gender, $country, $state]) {
    $password = $email === DEMO_EMAIL ? DEMO_PASSWORD : bin2hex(random_bytes(16));
    $created = date('Y-m-d H:i:s', strtotime('-' . (300 - $i * 20) . ' days'));
    $insertUser->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $gender, $country, $state, $created, security_questions()[0], password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), $created]);
    $userIds[] = (int) $pdo->lastInsertId();
}

echo 'Buscando capas no Open Library';
$bookIds = [];
$insertBook = $pdo->prepare('INSERT INTO books (title, author, release_year, pages, genre, characters, summary, cover_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($books as $i => [$title, $author, $year, $pages, $genre, $characters, $summary]) {
    $meta = open_library_meta($title, $author);
    echo '.';
    $insertBook->execute([$title, $author, $year, $pages, $genre, json_encode($characters, JSON_UNESCAPED_UNICODE), $summary, $meta['cover_url'], date('Y-m-d H:i:s', strtotime('-' . (280 - $i * 8) . ' days'))]);
    $bookIds[] = (int) $pdo->lastInsertId();
}
echo "\n";

$insertReading = $pdo->prepare('INSERT INTO user_books (user_id, book_id, status, rating, read_at, review, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
$statuses = ['Ja li', 'Ja li', 'Ja li', 'Lendo', 'Quero ler', 'Nao li'];
$total = 0;
foreach ($userIds as $u => $userId) {
    $picked = $bookIds;
    shuffle($picked);
    $picked = array_slice($picked, 0, $u === 0 ? 12 : mt_rand(7, 13));
    foreach ($picked as $k => $bookId) {
        $status = $u === 0 && $k === 0 ? 'Lendo' : $statuses[array_rand($statuses)];
        $daysAgo = mt_rand(1, 240);
        $rating = $status === 'Ja li' ? [5, 5, 4, 4, 4, 3, 2][mt_rand(0, 6)] : null;
        $review = $rating && mt_rand(1, 100) <= 70 ? $reviews[$rating][array_rand($reviews[$rating])] : '';
        $readAt = $status === 'Ja li' ? date('Y-m-d', strtotime("-{$daysAgo} days")) : null;
        $insertReading->execute([$userId, $bookId, $status, $rating, $readAt, $review, date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -" . mt_rand(0, 1439) . ' minutes'))]);
        $total++;
    }
}

$pdo->commit();
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec('VACUUM');

if (!copy(DB_PATH, DEMO_BASE_PATH)) {
    fwrite(STDERR, "Falha ao gravar " . DEMO_BASE_PATH . "\n");
    exit(1);
}
file_put_contents(DEMO_STAMP_PATH, (string) time());

printf("OK: %d leitores, %d livros, %d leituras. Ponto de restauracao gravado em data/demo_base.sqlite\n", count($userIds), count($bookIds), $total);
