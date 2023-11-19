
window.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('demographic-form').addEventListener('submit', function(e) {
        e.preventDefault();

        let name = document.getElementById('name').value;
        let age = document.getElementById('age').value;
        let gender = document.getElementById('gender').value;

        document.getElementById('result').innerHTML = `
            <p>Name: ${name}</p>
            <p>Alter: ${age}</p>
            <p>Geschlecht: ${gender}</p>
        `;
    });
});
