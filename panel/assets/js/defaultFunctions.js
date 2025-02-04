export function fetchData(body) {
    return fetch('assets/php/functions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(body)
    })
    .then(response => response.json())
    .catch(err => {
        console.error('Error fetching events data:', err);
        return null;
    });
}