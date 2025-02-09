let calculatingLove = false;

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function setPercentage(percentage) {
    const clipRect = document.getElementById('clipRect');
    const percentDisplay = document.querySelector(".percentageText");

    if (!clipRect || !percentDisplay) {
        return;
    }

    if (percentage !== 0) {
        const p = percentage / 100;
        const fillHeight = 80 * p; 
        const newY = 90 - fillHeight;

        clipRect.setAttribute('y', newY);
        clipRect.setAttribute('height', fillHeight);

        const timeBetweenUpdates = 1000 / percentage;
        for (let i = 0; i <= percentage; i++) {
            setTimeout(() => {
                percentDisplay.textContent = `${i}%`;
            }, timeBetweenUpdates * i);
        }

        document.querySelector(".love-tester-form .test_love").innerHTML = "Liefde wordt berekend...";
        await sleep(1000);

    } else {
        const currentPercentage = parseInt(percentDisplay.textContent) || 0;

        if (currentPercentage > 0) {
            clipRect.setAttribute('y', 90);
            clipRect.setAttribute('height', 0);

            let timeBetweenUpdates = 1000 / currentPercentage;
            for (let i = currentPercentage; i >= 0; i--) {
                setTimeout(() => {
                    percentDisplay.textContent = `${i}%`;
                }, timeBetweenUpdates * (currentPercentage - i));
            }

            document.querySelector(".love-tester-form .test_love").innerHTML = "Liefde wordt gereset...";
            await sleep(1250);
        }
    }
}

document.querySelector('.love-tester-form .test_love').addEventListener('click', async function () {
    if (calculatingLove) {
        return;
    }
    calculatingLove = true;
    try {
        const name1 = document.querySelector('.love-tester-form .name1').value;
        const name2 = document.querySelector('.love-tester-form .name2').value;

        if (!name1 || !name2) {
            notifications.show('Vul allebei de namen in.', 'error');
            return;
        }

        await setPercentage(0);

        const data = await defaultFunctions.fetchData({
            function: 'testLove',
            name1: name1,
            name2: name2,
        });

        if (data.success) {
            await setPercentage(data.percentage);

            const noHistoryP = document.querySelector('.history-container .no-history');
            const clearHistoryBtn = document.querySelector('.history-container .clear-history');
            if (noHistoryP && noHistoryP.style.display !== 'none') {
                noHistoryP.style.display = 'none';   
            }

            if (clearHistoryBtn && clearHistoryBtn.style.display !== 'block') {
                clearHistoryBtn.style.display = 'block';
            }

            const historyUl = document.querySelector('.history-container .history-list');

            const li = document.createElement('li');

            const span = document.createElement('span');
            span.innerHTML = `<strong>${name1}</strong> &amp; <strong>${name2}</strong> - ${data.percentage}%`;
            li.appendChild(span);

            const shareBtn = document.createElement('button');
            shareBtn.classList.add('share-button');
            shareBtn.innerHTML = 'Deel';
            shareBtn.setAttribute('onclick', `loveTesterShare.shareMenu(${data.historyId})`);
            li.appendChild(shareBtn);

            historyUl.prepend(li);
            document.querySelector('.love-tester-form .test_love').innerHTML = 'Berekenen';
        } else {
            notifications.show(data.error, 'error');
        }
    } catch (error) {
        console.error('Er ging iets mis:', error);
    } finally {
        calculatingLove = false;
    }
});

document.querySelector('.history-container .clear-history').addEventListener('click', async function () {
    try {
        const data = await defaultFunctions.fetchData({
            function: 'clearLoveTesterHistory',
        });

        if (data.success) {
            const historyUl = document.querySelector('.history-container .history-list');
            historyUl.innerHTML = '';

            const noHistoryP = document.querySelector('.history-container .no-history');
            if (noHistoryP && noHistoryP.style.display !== 'block') {
                noHistoryP.style.display = 'block';
            }

            const clearHistoryBtn = document.querySelector('.history-container .clear-history');
            if (clearHistoryBtn && clearHistoryBtn.style.display !== 'none') {
                clearHistoryBtn.style.display = 'none';
            }
        } else {
            notifications.show(data.error, 'error');
        }
    } catch (error) {
        console.error('Er ging iets mis:', error);
    }
});