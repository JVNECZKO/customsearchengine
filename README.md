# Custom Prestashop Search Engine

## This module alows to redirect into direct combination if user enter MPN Code or Reference Code

### Instalation & Usage

1. Install module
2. Put HTML Snippet in place you want 
'<form id="searchbyrefmpn-form">
    <input type="text" id="searchbyrefmpn-input" placeholder="Enter reference code or MPN">
    <button class="submit-nav" type="submit">Search</button>
    <ul id="suggestions-list" style="display: none;"></ul>
</form>'
3. Add an CSS styling ' #suggestions-list {
    border: 1px solid #ccc;
    max-height: 400px;
    overflow-y: auto;
    position: absolute;
    background: white;
    list-style: none;
    padding: 0;
    margin: 0;
    width: calc(100%);
}

#suggestions-list li {
    padding: 10px;
    cursor: pointer;
}

#suggestions-list li:hover {
    background: #f0f0f0;
}

#searchbyrefmpn-input {
    padding: 15px;
    border: none;
    border-bottom: 1px solid #0071bc;
    width: 300px;
}

.submit-nav {
    background-color: #fff;
    padding: 15px;
    border:none;
}

.submit-nav:hover {
    background-color: #0071bc;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
}'
4.done
