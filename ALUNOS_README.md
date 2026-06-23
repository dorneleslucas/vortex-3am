# CRUD de Alunos - Vortex

## 📋 Resumo

Sistema completo de CRUD (Create, Read, Update, Delete) para gerenciar alunos vinculados a personals.

## 📁 Arquivos Criados

### Backend (PHP)
- **Model**: `/api/source/Models/Aluno.php`
- **Controller**: `/api/source/Controller/Alunos.php`
- **Rotas**: Adicionadas em `/api/index.php`

### Frontend (JavaScript)
- **API Client**: `/views/js/alunos-api.js`
- **UI Manager**: `/views/js/alunos-manager.js`
- **HTML Template**: `/views/alunos.html`

---

## 🔌 Endpoints da API

### GET - Listar todos os alunos
```
GET /api/alunos/list
```
**Resposta:**
```json
{
  "code": 200,
  "status": "success",
  "message": "Lista de alunos",
  "data": [
    {
      "id": 1,
      "personal_id": 2,
      "nome": "Marina Costa",
      "email": "marina@email.com",
      "telefone": "(51) 99999-1111",
      "objetivo": "Hipertrofia",
      "ativo": 1,
      "personal_nome": "Rafael Personal"
    }
  ]
}
```

### GET - Buscar aluno por ID
```
GET /api/alunos/list/{id}
```

### GET - Listar alunos de um personal
```
GET /api/alunos/personal/{personal_id}
```

### POST - Criar novo aluno
```
POST /api/alunos/
Content-Type: application/json

{
  "personal_id": 2,
  "nome": "João Silva",
  "email": "joao@email.com",
  "telefone": "(51) 99999-2222",
  "objetivo": "Emagrecimento",
  "ativo": 1
}
```

### PUT - Atualizar aluno
```
PUT /api/alunos/{id}
Content-Type: application/json

{
  "personal_id": 2,
  "nome": "João Silva Atualizado",
  "email": "joao_novo@email.com",
  "telefone": "(51) 99999-3333",
  "objetivo": "Força",
  "ativo": 1
}
```

### DELETE - Deletar aluno
```
DELETE /api/alunos/{id}
```

---

## 💻 Uso do JavaScript

### Classe AlunosAPI

```javascript
const api = new AlunosAPI();

// Listar todos os alunos
await api.listar();

// Buscar um aluno
await api.buscar(1);

// Buscar alunos de um personal
await api.buscarPorPersonal(2);

// Criar aluno
await api.criar({
  personal_id: 2,
  nome: "Ana Silva",
  email: "ana@email.com",
  telefone: "(51) 99999-4444",
  objetivo: "Flexibilidade"
});

// Atualizar aluno
await api.atualizar(1, {
  nome: "Ana Silva Atualizado",
  objetivo: "Resistência"
});

// Deletar aluno
await api.excluir(1);
```

### Classe AlunosManager

Gerencia a interface do usuário automaticamente. Eventos:

- **#btn-novo-aluno**: Abre formulário para novo aluno
- **#btn-salvar-aluno**: Salva aluno (criar ou atualizar)
- **#btn-cancelar-aluno**: Fecha o formulário
- **Botões Editar/Deletar na tabela**: Automáticos

---

## 📝 Campos do Aluno

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | integer | Auto | ID único |
| personal_id | integer | ✅ | ID do personal responsável |
| nome | string | ✅ | Nome completo do aluno |
| email | string | ❌ | Email do aluno |
| telefone | string | ❌ | Telefone para contato |
| objetivo | string | ❌ | Objetivo do treinamento |
| ativo | integer | ✅ | Status (1=ativo, 0=inativo) |

---

## 🧪 Testando a API

### Com cURL

```bash
# Listar
curl http://localhost/api/alunos/list

# Criar
curl -X POST http://localhost/api/alunos/ \
  -H "Content-Type: application/json" \
  -d '{"personal_id":2,"nome":"Test","objetivo":"Força"}'

# Atualizar
curl -X PUT http://localhost/api/alunos/1 \
  -H "Content-Type: application/json" \
  -d '{"personal_id":2,"nome":"Test Updated"}'

# Deletar
curl -X DELETE http://localhost/api/alunos/1
```

### Com JavaScript

```javascript
const alunosAPI = new AlunosAPI();

// Testar listar
alunosAPI.listar().then(resultado => {
  console.log('Alunos:', resultado.data);
});
```

---

## 🎯 Como Usar na Sua Página

1. Inclua os scripts na sua página HTML:
```html
<script src="js/alunos-api.js"></script>
<script src="js/alunos-manager.js"></script>
```

2. Use o template em `/views/alunos.html` como referência

3. Customize os elementos HTML conforme sua estrutura

4. A classe `AlunosManager` cuidará de:
   - Carregar dados ao inicializar
   - Renderizar a tabela
   - Abrir/fechar formulário
   - Validar dados
   - Fazer chamadas à API
   - Mostrar mensagens de sucesso/erro

---

## ⚙️ Configuração

O token é buscado automaticamente de:
1. `localStorage` com chave `token`
2. `sessionStorage` com chave `token`
3. Vazio se não encontrar

Para definir o token:
```javascript
localStorage.setItem('token', seu_token_aqui);
```

---

## 🐛 Troubleshooting

### Erro 401 Unauthorized
- Verifique se o token está sendo enviado no header
- Verifique se o token é válido

### Erro 400 Bad Request
- Verifique se `personal_id` e `nome` estão preenchidos
- Verifique se `personal_id` é um número válido

### Erro 404 Not Found
- Aluno com este ID não existe
- Verifique o ID do aluno

### Erro 500 Internal Server Error
- Verifique os logs do servidor
- Verifique se a conexão com banco de dados está ok
