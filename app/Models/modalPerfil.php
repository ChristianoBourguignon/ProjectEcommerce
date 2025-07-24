<!-- Modal Login/Cadastro -->
<div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="perfilModalLabel">Acesse sua conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Abas -->
                <ul class="nav nav-tabs" id="loginTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Cadastro</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recovery-tab" data-bs-toggle="tab" data-bs-target="#recovery" type="button" role="tab">Recuperar Senha</button>
                    </li>

                </ul>

                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="loginTabContent">
                    <!-- Login -->
                    <div class="tab-pane fade show active p-4" id="login" role="tabpanel">
                        <form action="/logar" method="POST">
                             <div class="mb-3">
                                <label for="loginCPF" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="loginCpf" name="loginCpf" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s">Entrar</button>
                        </form>
                    </div>
                    <!-- Cadastro -->
                    <div class="tab-pane fade p-4" id="register" role="tabpanel">
                        <form id="registerForm" action="/criarConta" method="POST">
                            <div class="mb-3">
                                <label for="registerName" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="registerName" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="registerEmail" name="email" required>
                                <div class="invalid-feedback">Email inválido.</div>
                            </div>
                            <div class="mb-3">
                                <label for="registerCpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="registerCpf" name="cpf" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s" id="registerBtn" disabled>Cadastrar</button>
                        </form>
                    </div>
                <!-- Recuperar Senha -->
                    <div class="tab-pane fade p-4" id="recovery" role="tabpanel">
                        <form id="recoveryForm">
                            <div class="mb-3">
                                <label for="recoveryEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="recoveryEmail" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s" id="recoveryBtn" disabled>Enviar Link</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="respostaModal" tabindex="-1" aria-labelledby="respostaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="respostaModalLabel">Recuperação de Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="respostaMensagem"></div>
        </div>
    </div>
</div>


<script>
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    document.addEventListener("DOMContentLoaded", () => {
        const emailInput = document.getElementById("registerEmail");
        const senhaInput = document.getElementById("registerPassword");
        const confirmInput = document.getElementById("confirmPassword");
        const btnCadastrar = document.getElementById("registerBtn");

        function validarCadastro() {
            const emailValido = validarEmail(emailInput.value);
            // Email
            emailInput.classList.toggle("is-invalid", !emailValido);

            // Habilita o botão se tudo estiver certo
            btnCadastrar.disabled = !(emailValido);
        }

        [emailInput, senhaInput, confirmInput].forEach(el => {
            el.addEventListener("input", validarCadastro);
            el.addEventListener("blur", validarCadastro); // mostra após sair do campo
        });

        // Login
        const loginEmail = document.getElementById("loginEmail");
        const loginBtn = document.querySelector("#login button[type='submit']");
        loginEmail.addEventListener("input", () => {
            loginBtn.disabled = !validarEmail(loginEmail.value);
        });
        loginBtn.disabled = true;

        // Recuperação
        const recoveryEmail = document.getElementById("recoveryEmail");
        const recoveryBtn = document.getElementById("recoveryBtn");
        recoveryEmail.addEventListener("input", () => {
            recoveryBtn.disabled = !validarEmail(recoveryEmail.value);
        });
    });
        document.getElementById("recoveryForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const email = document.getElementById("recoveryEmail").value;

        fetch("backend/recuperarSenha.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
        body: new URLSearchParams({ email: email })
    })
        .then(response => response.json())
        .then(data => {
        const mensagem = document.getElementById("respostaMensagem");
        mensagem.textContent = data.message;

        if (data.success) {
        mensagem.classList.remove("text-danger");
        mensagem.classList.add("text-success");
    } else {
        mensagem.classList.remove("text-success");
        mensagem.classList.add("text-danger");
    }

        const modal = new bootstrap.Modal(document.getElementById('respostaModal'));
        modal.show();
    })
        .catch(error => {
        console.error("Erro:", error);
    });
    });

</script>
