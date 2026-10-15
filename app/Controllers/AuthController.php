<?php
declare(strict_types=1);

final class AuthController
{
    private Usuario $usuarios;

    public function __construct()
    {
        $this->usuarios = new Usuario();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
    }

    public function mostrarLogin(): void
    {
        if (!empty($_SESSION['usuario'])) {
            $this->redirecionar('painel');
        }
        $erro = $_SESSION['erro'] ?? '';
        unset($_SESSION['erro']);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        $this->validarCsrf();
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = (string) ($_POST['senha'] ?? '');
        if (!$email || $senha === '') {
            $_SESSION['erro'] = 'Informe um e-mail válido e sua senha.';
            $this->redirecionar('login');
        }
        try {
            $usuario = $this->usuarios->buscarPorEmail($email);
        } catch (Throwable $erro) {
            error_log($erro->getMessage());
            $_SESSION['erro'] = 'Não foi possível conectar ao sistema. Confira a configuração do banco.';
            $this->redirecionar('login');
        }
        if (!$usuario || !(bool) $usuario['ativo'] || !password_verify($senha, $usuario['senha'])) {
            $_SESSION['erro'] = 'E-mail ou senha inválidos.';
            $this->redirecionar('login');
        }
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfis' => array_filter(explode(',', (string) $usuario['perfis'])),
        ];
        $arquivoId=(int)($_SESSION['abrir_arquivo_comprovante_apos_login']??0);
        $revisaoId=(int)($_SESSION['abrir_revisao_comprovante_apos_login']??0);
        unset($_SESSION['abrir_arquivo_comprovante_apos_login'],$_SESSION['abrir_revisao_comprovante_apos_login']);
        if($arquivoId>0){header('Location: index.php?rota=baixar_comprovante&id='.$arquivoId);exit;}
        if($revisaoId>0){header('Location: index.php?rota=abrir_comprovante&id='.$revisaoId.'#comprovante-'.$revisaoId);exit;}
        $this->redirecionar('painel');
    }

    public function mostrarCadastro(): void
    {
        if (!empty($_SESSION['usuario'])) {
            $this->redirecionar('painel');
        }
        $erro = $_SESSION['erro'] ?? '';
        unset($_SESSION['erro']);
        require __DIR__ . '/../Views/auth/cadastro.php';
    }

    public function cadastrar(): void
    {
        $this->validarCsrf();
        $nome = trim($_POST['nome'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = (string) ($_POST['senha'] ?? '');
        $perfisPermitidos = ['professor', 'aluno'];
        $perfis = array_values(array_intersect($perfisPermitidos, (array) ($_POST['perfis'] ?? [])));

        if (mb_strlen($nome) < 2 || !$email || strlen($senha) < 8 || !$perfis) {
            $_SESSION['erro'] = 'Confira os dados. A senha precisa ter ao menos 8 caracteres e selecione um ou mais acessos.';
            $this->redirecionar('cadastro');
        }
        try {
            if ($this->usuarios->buscarPorEmail($email)) {
                $_SESSION['erro'] = 'Já existe uma conta com esse e-mail.';
                $this->redirecionar('cadastro');
            }
            $this->usuarios->cadastrar($nome, $email, $senha, $perfis);
            $_SESSION['mensagem'] = 'Conta criada. Entre com seu e-mail e senha.';
            $this->redirecionar('login');
        } catch (Throwable $erro) {
            error_log($erro->getMessage());
            $_SESSION['erro'] = 'Não foi possível criar a conta. Confira a conexão e tente novamente.';
            $this->redirecionar('cadastro');
        }
    }

    public function painel(): void
    {
        if (empty($_SESSION['usuario'])) {
            $this->redirecionar('login');
        }
        $usuario = $_SESSION['usuario'];
        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function sair(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
        }
        session_destroy();
        $this->redirecionar('login');
    }

    private function validarCsrf(): void
    {
        $token = (string) ($_POST['csrf'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
            http_response_code(419);
            exit('A sessão expirou. Atualize a página e tente novamente.');
        }
    }

    private function redirecionar(string $rota): void
    {
        header('Location: index.php?rota=' . rawurlencode($rota));
        exit;
    }
}
