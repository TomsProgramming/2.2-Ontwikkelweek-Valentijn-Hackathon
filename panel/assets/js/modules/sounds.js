let soundConfig = null;

async function getSoundsConfig() {
    try {
        const soundsConfig = await defaultFunctions.fetchData({
            function: 'getSoundsConfig'
        });

        if (!soundsConfig.success || !soundsConfig.sounds) {
            console.warn("Geen geldige soundconfig ontvangen.");
            return;
        }

        const soundConfig = soundsConfig.sounds;

        if (soundConfig.backgroundSound && soundConfig.backgroundSound !== "nothing") {
            const audio = new Audio('../sounds/background.mp3');
            audio.volume = 0.01;

            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {
                    document.addEventListener("click", () => {
                        audio.play().catch(error => console.error(error));
                    }, { once: true });
               
                });
            }
        }
    } catch (error) {
        console.error("Fout in getSoundsConfig:", error);
    }
}


function playSound(soundPath, soundName) {
    if (typeof soundName === "string" && soundName.trim() && soundName !== "nothing") {
        const audio = new Audio(`${soundPath}/${soundName}.mp3`);
        audio.play().catch(error => console.error(error));
    }
}

export function newMessage() {
    if (soundConfig && typeof soundConfig.notificationSound === "string") {
        playSound("../sounds/notifications", soundConfig.notificationSound);
    }
}

export function sendMessage() {
    if (soundConfig && typeof soundConfig.sendMessageSound === "string") {
        playSound("../sounds/sendMessage", soundConfig.sendMessageSound);
    }
}

getSoundsConfig();