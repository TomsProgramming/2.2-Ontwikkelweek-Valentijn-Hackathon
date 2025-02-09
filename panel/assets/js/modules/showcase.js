let player;
let timeout;
let inShowcase = false;
let qualityObserver;

const showcaseContainer = document.getElementById("showcase-container");
const exitMessage = document.getElementById("exit-message");

if (localStorage.getItem('showcase') === 'true') {
    const iframe = document.getElementById('showcase');
    const navBar = document.querySelector('.nav-bar');

    if (!iframe || !navBar || !showcaseContainer || !exitMessage) {
        console.error("⚠️ Vereiste elementen niet gevonden.");
    } else {
        function moveMouse() {
            if (inShowcase) {
                exitShowcase();
            } else {
                clearTimeout(timeout);
                timeout = setTimeout(startShowcase, 10000);
            }
        }

        function startShowcase() {
            inShowcase = true;
            showcaseContainer.style.display = 'block';
            navBar.style.display = 'none';
            document.body.style.background = '#fc828d';

            exitMessage.style.display = "block";
            setTimeout(() => {
                exitMessage.style.opacity = "1";
            }, 100);

            if (player) {
                player.playVideo();
                setTimeout(setMaxQuality, 500);
                setInterval(setMaxQuality, 3000);
            }
        }

        function exitShowcase() {
            inShowcase = false;
            showcaseContainer.style.display = "none";
            navBar.style.display = 'flex';
            document.body.style.background = 'linear-gradient(135deg, #f78ca0, #f9748f, #fd868c, #fe9a8b)';

            exitMessage.style.animation = "none";
            exitMessage.style.opacity = "0";
            setTimeout(() => {
                exitMessage.style.display = "none";
            }, 500);

            if (player) {
                player.pauseVideo();
            }
        }

        document.addEventListener("visibilitychange", () => {
            if (document.hidden && inShowcase) {
                exitShowcase();
            }
        });

        document.addEventListener("mousemove", moveMouse);
        moveMouse();
    }
}

var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubeIframeAPIReady() {
    player = new YT.Player('showcase', {
        events: {
            'onReady': onPlayerReady
        }
    });
}

function onPlayerReady(event) {
    event.target.playVideo();
    setTimeout(setMaxQuality, 500);
    setInterval(setMaxQuality, 3000);
    observeQualityChange();
}

function observeQualityChange() {
    if (!player || !player.getIframe()) return;

    const observer = new MutationObserver(() => {
        console.log("🔄 Kwaliteit werd aangepast door YouTube, herstel naar hoogste!");
        setMaxQuality();
    });

    observer.observe(player.getIframe(), {
        childList: true,
        subtree: true
    });

    qualityObserver = observer;
}

function setMaxQuality() {
    if (player && player.getAvailableQualityLevels) {
        let qualityLevels = player.getAvailableQualityLevels();
        if (qualityLevels.length > 0) {
            let highestQuality = qualityLevels[0];
            player.setPlaybackQuality(highestQuality);
            console.log("🎥 Videokwaliteit geforceerd op:", highestQuality);
        }
    }
}
