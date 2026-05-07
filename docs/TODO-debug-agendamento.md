# TODO-debug-agendamento

## Objetivo
Descobrir por que o submit de `consulta.html` não persiste em `agendamentos`.

## Passos
- [ ] Verificar no DevTools (Console e Network) se a requisição `server/api/agendamentos.php` está sendo feita.
- [ ] No Network, checar o Status Code e Body (JSON) retornado pelo PHP.
- [ ] Se não houver requisição: conferir se o seletor `document.querySelector('.form-group')` está apontando para o form correto.
- [ ] Se houver requisição com 422/500: verificar mensagem retornada por `agendamentos.php`.
- [ ] Checar o `error_log` do PHP (log do XAMPP) para o texto `Agendamento recebido:`.

