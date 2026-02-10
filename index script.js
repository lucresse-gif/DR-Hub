function openModal(type) {
    document.getElementById(type + 'Modal').classList.add('active');
}

function closeModal(type) {
    document.getElementById(type + 'Modal').classList.remove('active');
    document.getElementById(type + 'Error').style.display = 'none';
    document.getElementById(type + 'Success').style.display = 'none';
}

function switchModal(closeType, openType) {
    closeModal(closeType);
    openModal(openType);
}

function showMessage(type, formType, message) {
    const element = document.getElementById(formType + (type === 'error' ? 'Error' : 'Success'));
    element.textContent = message;
    element.style.display = 'block';
            
    setTimeout(() => {
        element.style.display = 'none';
    }, 5000);
}

async function handleSignup(event) {
    event.preventDefault();
            
    const username = document.getElementById('signup-username').value;
    const email = document.getElementById('signup-email').value;
    const password = document.getElementById('signup-password').value;
            
    try {
        const response = await fetch('register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: username,
            email: email,
            password: password
        })
    });
                
    const data = await response.json();
                
        if (data.success) {
            showMessage('success', 'signup', data.message);
            setTimeout(() => {
                window.location.href = 'dashboard.php';
                }, 2000);
        } else {
            showMessage('error', 'signup', data.message);
        }
    } catch (error) {
        showMessage('error', 'signup', 'Erreur de connexion au serveur');
    }
}

async function handleLogin(event) {
    event.preventDefault();
            
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
            
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });
                
        const data = await response.json();
                
        if (data.success) {
            showMessage('success', 'login', data.message);
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        } else {
            showMessage('error', 'login', data.message);
        }
    } catch (error) {
        showMessage('error', 'login', 'Erreur de connexion au serveur');
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}