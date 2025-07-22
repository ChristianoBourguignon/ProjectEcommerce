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
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Cadastro</button>-->
<!--                    </li>-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button class="nav-link" id="recovery-tab" data-bs-toggle="tab" data-bs-target="#recovery" type="button" role="tab">Recuperar Senha</button>-->
<!--                    </li>-->

                </ul>

                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="loginTabContent">
                    <!-- Login -->
                    <div class="tab-pane fade show active p-4" id="login" role="tabpanel">
                        <form action="/logar" method="POST">
                            <div class="mb-3">
                                <label for="loginName" class="form-label">Seu nome</label>
                                <input type="text" class="form-control" id="loginName" name="loginName" required>
                            </div>
<!--                            <div class="mb-3">-->
<!--                                <label for="loginEmail" class="form-label">Email</label>-->
<!--                                <input type="email" class="form-control" id="loginEmail" name="email" required>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label for="loginPassword" class="form-label">Senha</label>-->
<!--                                <input type="password" class="form-control" id="loginPassword" name="senha" required>-->
<!--                            </div>-->
                            <button type="submit" class="btn btn-primary btn-lg shadow animate__animated animate__pulse delay-2s">Entrar</button>
                        </form>
                    </div>
                    <!-- Cadastro -->
<!--                    <div class="tab-pane fade p-4" id="register" role="tabpanel">-->
<!--                        <form id="registerForm" action="/criarConta" method="POST">-->
<!--                            <div class="mb-3">-->
<!--                                <label for="registerName" class="form-label">Nome</label>-->
<!--                                <input type="text" class="form-control" id="registerName" name="nome" required>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label for="registerEmail" class="form-label">Email</label>-->
<!--                                <input type="email" class="form-control" id="registerEmail" name="email" required>-->
<!--                                <div class="invalid-feedback">Email inválido.</div>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label for="registerPassword" class="form-label">Senha</label>-->
<!--                                <input type="password" class="form-control" id="registerPassword" name="senha" required>-->
<!--                                <div class="invalid-feedback" id="senhaFeedback">-->
<!--                                    A senha deve conter no mínimo 6 caracteres, uma letra maiúscula, uma minúscula e um número.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label for="confirmPassword" class="form-label">Confirmar Senha</label>-->
<!--                                <input type="password" class="form-control" id="confirmPassword" required>-->
<!--                                <div class="invalid-feedback" id="confirmFeedback">-->
<!--                                    As senhas não coincidem.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <button type="submit" class="btn btn-success" id="registerBtn" disabled>Cadastrar</button>-->
<!--                        </form>-->
<!--                    </div>-->
<!--                    <!-- Recuperar Senha -->
<!--                    <div class="tab-pane fade p-4" id="recovery" role="tabpanel">-->
<!--                        <form id="recoveryForm">-->
<!--                            <div class="mb-3">-->
<!--                                <label for="recoveryEmail" class="form-label">Email</label>-->
<!--                                <input type="email" class="form-control" id="recoveryEmail" name="email" required>-->
<!--                            </div>-->
<!--                            <button type="submit" class="btn btn-warning" id="recoveryBtn" disabled>Enviar Link</button>-->
<!--                        </form>-->
<!--                    </div>-->
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

        const senhaFeedback = document.getElementById("senhaFeedback");
        const confirmFeedback = document.getElementById("confirmFeedback");

        function validarCadastro() {
            const emailValido = validarEmail(emailInput.value);
            const senhasIguais = senhaInput.value === confirmInput.value;

            // Email
            emailInput.classList.toggle("is-invalid", !emailValido);

            // Senha
            confirmFeedback.style.display = senhasIguais ? "none" : "block";

            // Habilita o botão se tudo estiver certo
            btnCadastrar.disabled = !(emailValido && senhasIguais);
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
