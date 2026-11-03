// Métodos compartilhados adicionados sem torná-los enumeráveis no protótipo global.
if (!String.prototype.toTitleCase) {
    Object.defineProperty(String.prototype, "toTitleCase", {
        configurable: true,
        value() { 
            return this.trim().toLocaleLowerCase("pt-BR").replace(/(^|\s)\p{L}/gu, letra => letra.toLocaleUpperCase("pt-BR")); }
    });
}
if (!Array.prototype.sortBy) {
    Object.defineProperty(Array.prototype, "sortBy", {
        configurable: true,
        value(chave, direcao = "asc") {
            const fator = direcao === "desc" ? -1 : 1;
            return [...this].sort((a, b) => String(a?.[chave] ?? "").localeCompare(String(b?.[chave] ?? ""), "pt-BR", { numeric: true }) * fator);
        }
    });
}
