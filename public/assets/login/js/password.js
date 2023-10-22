// Affichage mot de passe Sign In
const passwordTnput = document.getElementById("check-password");

passwordTnput.addEventListener("input", function () {
    const password = this.value;
    showPassord(password);

    // Affiche mdp pendant X temps
    setTimeout(() => {
        hidePassword();
    }, 2000);
});


function showPassord(password) {
    passwordTnput.type = "text";
    passwordTnput.value = password;
}

function hidePassword() {
    passwordTnput.type = "password";
}


// Affichage mot de passe Login
const passwordTnput2 = document.getElementById("check2-password");

passwordTnput2.addEventListener("input", function () {
    const password = this.value;
    showPassord2(password);

    // Affiche mdp pendant X temps
    setTimeout(() => {
        hidePassword2();
    }, 2000);
});


function showPassord2(password) {
    passwordTnput2.type = "text";
    passwordTnput2.value = password;
}

function hidePassword2() {
    passwordTnput2.type = "password";
}