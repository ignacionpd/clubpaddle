// Seleccionamos las variables y los inputs del formulario
const login_form = document.querySelector('#login_form');
const userLoginName = document.querySelector('#user_login_name');
const userPassword = document.querySelector('#user_password');

function validateLoginName(user_login_name){
    let regex = /^[a-zA-Z0-9]{6,10}$/;

    return regex.test(user_login_name);
}

function validatePassword(user_password){
    let regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[.,_\-])[a-zA-Z\d.,_\-]{4,10}$/;

    return regex.test(user_password);
}

// Definimos las funciones de validación que se ejecutarán al salir del input
function validateOnBlur(inputElement, validator){

    if (!inputElement) return;
    inputElement.addEventListener('blur', function(){
        let value = inputElement.value;
        let valid = validator(value);
        let smallElement = inputElement.nextElementSibling; // Encuentra el elemento small

        if(!valid){
            smallElement.textContent = "Error: El contenido introducido no es válido";
            smallElement.style.color = "red";
            smallElement.style.visibility = "visible";
        }else{
            smallElement.style.visibility = "hidden"; // Escondemos el campo
            smallElement.textContent = ''; // Liampiamos el campo
        }

    });
}

// Capturamos el evento del envío del formulario para controlar si hay errores.
if(login_form) {
    login_form.addEventListener('submit', function(e){
        let isUserLoginNameValid = validateLoginName(userLoginName.value);
        let isPasswordValid = validatePassword(userPassword.value);

        if (!isUserLoginNameValid || !isPasswordValid){
            // Prevenimos el envío del formulario
            e.preventDefault();
        }
    });
};

// Ejecutamos las funciones de validación
validateOnBlur(userLoginName, validateLoginName);
validateOnBlur(userPassword, validatePassword);

