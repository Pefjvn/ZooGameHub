<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Donation Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #98d7ef 0%, #f7f1d7 100%);
            color: #2c3e50;
        }
        .hero {
            text-align: center;
            padding: 40px 20px;
        }
        .hero h1 {
            margin: 0;
            font-size: 3rem;
        }
        .hero p {
            margin: 10px auto 0;
            max-width: 600px;
            line-height: 1.6;
        }
        .container {
            max-width: 620px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        }
        .amount-options {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
        }
        .amount-options button {
            flex: 1 1 120px;
            padding: 14px 18px;
            border: none;
            border-radius: 10px;
            background: #ffb347;
            color: #2c3e50;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.15s ease;
        }
        .amount-options button:hover,
        .amount-options button.active {
            background: #ffdd73;
            transform: translateY(-2px);
        }
        .custom-amount {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .custom-amount label {
            font-weight: bold;
        }
        .custom-amount input {
            padding: 12px 14px;
            border: 2px solid #d1d8e0;
            border-radius: 10px;
            font-size: 1rem;
        }
        .confirm {
            display: block;
            width: 100%;
            padding: 16px;
            margin-top: 20px;
            border: none;
            border-radius: 12px;
            background: #4caf50;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .summary {
            margin-top: 20px;
            padding: 14px 18px;
            border-radius: 12px;
            background: #f0f8ff;
            border: 1px solid #c3d8f3;
        }
        
        button:active {
            transform: scale(0.95);
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #8bcfff 0%, #76aef4 100%);
            color: rgb(0, 1, 1);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            transform: scale(1.05);
        }
        .back-button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <a href="homepage.html" class="back-button">← Back</a>
    <section class="hero">
        <h1>Support Our Zoo</h1>
        <p>Help care for the animals, protect habitats, and support educational programs. Choose a donation amount below or enter your own contribution.</p>
        <p><strong>Contact: Aiden Tran</strong> | Phone: 827-958-2738</p>
    </section>
    <main class="container">
        <form id="donationForm">
            <div class="amount-options" role="group" aria-label="Donation amounts">
                <button type="button" class="preset" data-amount="10">$10</button>
                <button type="button" class="preset" data-amount="50">$50</button>
                <button type="button" class="preset" data-amount="100">$100</button>
                <button type="button" class="preset" data-amount="other">Other</button>
            </div>
            <div class="custom-amount">
                <label for="customAmount">Custom amount</label>
                <input id="customAmount" name="customAmount" type="number" min="1" placeholder="Enter amount" aria-describedby="amountHelp" />
                <small id="amountHelp">Enter an amount in dollars if you want to donate a different amount.</small>
            </div>
            <button class="confirm" type="submit">Confirm Donation</button>
        </form>
        <div class="summary" id="summary">
            Selected donation: <strong>$0</strong>
        </div>
    </main>
    <script>
        const presetButtons = document.querySelectorAll('.preset');
        const customAmount = document.getElementById('customAmount');
        const summary = document.getElementById('summary');
        let selectedAmount = 0;

        function updateSummary(amount) {
            summary.innerHTML = 'Selected donation: <strong>$' + amount + '</strong>';
        }

        presetButtons.forEach(button => {
            button.addEventListener('click', () => {
                presetButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                const amount = button.dataset.amount;
                if (amount === 'other') {
                    customAmount.focus();
                    selectedAmount = Number(customAmount.value) || 0;
                } else {
                    selectedAmount = Number(amount);
                    customAmount.value = '';
                }
                updateSummary(selectedAmount);
            });
        });

        customAmount.addEventListener('input', () => {
            presetButtons.forEach(btn => btn.classList.remove('active'));
            selectedAmount = Number(customAmount.value) || 0;
            updateSummary(selectedAmount);
        });

        document.getElementById('donationForm').addEventListener('submit', event => {
            event.preventDefault();
            let amount = selectedAmount;
            if (!amount) {
                amount = Number(customAmount.value) || 0;
            }
            if (amount < 1) {
                alert('Please select or enter a donation amount.');
                return;
            }
            alert('Thank you for donating $' + amount + ' to the zoo!');
        });
    </script>
</body>
</html>