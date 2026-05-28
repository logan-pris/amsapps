// Toggle between Login and Register
function toggleFlip() {
    document.querySelector('.flipper').classList.toggle('flipped');
}

// registration logic
async function handleRegistration() {
    const emailInput = document.getElementById('regEmail');
    const email = emailInput.value.trim().toLowerCase();
    const notice = document.getElementById('emailNotice');

    // Email Domain Guard
    if (!email.endsWith('@ams.events')) {
        notice.textContent = "Access denied. Use your @ams.events email.";
        notice.style.display = 'block';
        return;
    }
    notice.style.display = 'none';

    // Call API (Moved to /api/ folder)
    try {
        const response = await fetch('api/request_code.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        });
        const result = await response.json();

        if (result.success) {
            document.getElementById('targetEmail').textContent = email;
            document.getElementById('reg-step-1').style.display = 'none';
            document.getElementById('reg-step-2').style.display = 'block';
        } else {
            alert(result.message);
        }
    } catch (e) {
        alert("An error occurred. Please check your connection.");
    }
}

async function verifyRegistrationCode() {
    const email = document.getElementById('targetEmail').textContent;
    const code = document.getElementById('verifyCode').value.trim();

    const response = await fetch('api/verify_code.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, code: code })
    });

    const result = await response.json();
    if (result.success && result.step === 'password_entry') {
        document.getElementById('reg-step-2').style.display = 'none';
        document.getElementById('reg-step-3').style.display = 'block';
    } else {
        alert(result.message || "Invalid code.");
    }
}

async function completeRegistration() {
    const email = document.getElementById('targetEmail').textContent;
    const code = document.getElementById('verifyCode').value.trim();
    const password = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (password.length < 8) return alert("Password must be 8+ characters.");
    if (password !== confirm) return alert("Passwords do not match.");

    const response = await fetch('api/verify_code.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, code: code, password: password })
    });

    const result = await response.json();
    if (result.success) {
        alert("Account Created! You can now login.");
        window.location.reload(); 
    } else {
        alert(result.message);
    }
}