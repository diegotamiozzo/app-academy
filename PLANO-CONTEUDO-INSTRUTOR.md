# Plano de Ação: Conteúdo do Instrutor (Cursos, Módulos e Materiais)

**Projeto:** Optimize Academy (`app-academy`)
**Branch de referência:** `develop`
**Stack:** Laravel, Livewire 4 (Volt), Flux UI, Fortify, Tailwind, Vite, Pest
**Status:** MVP das fases 1 a 4 implementado; embeds e download mantêm o aluno na plataforma; fase 5 validada com testes e build

---

## 1. Objetivo

Permitir que o **instrutor** de uma empresa publique cursos na plataforma, com conteúdo hospedado externamente (Google Slides, YouTube e Google Drive), e libere o acesso apenas para usuários **da mesma empresa**. O banco de dados armazena somente os **identificadores dos links**; os arquivos permanecem no Google/YouTube.

## 2. Estrutura de conteúdo (definição final)

**Curso**
- Título, descrição e status (rascunho ou publicado).
- **Material complementar** (opcional): um único PDF do Drive, no final do curso, com todo o conteúdo e o checkbox **"permitir download"**.

**Módulos** (quantos forem necessários, reordenáveis)
- Título e posição.
- **Link do Google Slides**: obrigatório. O usuário avança os slides dentro da plataforma.
- **Link do YouTube**: opcional. O módulo funciona normalmente sem vídeo.

**Acesso**
- O instrutor seleciona quais usuários da própria empresa podem acessar cada curso.

## 3. Fluxos

### Instrutor
1. Cria o curso (dados básicos).
2. Adiciona módulos (botão "adicionar módulo") e reordena. O total é calculado, sem campo de "quantidade".
3. Em cada módulo cola o link do Slides e, se quiser, o do YouTube.
4. No fim do formulário, informa o PDF complementar e marca ou não "permitir download".
5. Seleciona os usuários com acesso.
6. Publica.

### Usuário (aluno)
1. Vê somente cursos **publicados**, da **sua empresa** e **liberados** para ele.
2. Abre o curso e percorre os módulos em ordem (slides e vídeo, quando houver).
3. Ao final, acessa o **Material complementar**. O botão de baixar aparece apenas se o instrutor permitiu.

## 4. Modelo de dados

Guardar o **ID extraído do link**, e não a URL colada. O servidor monta a URL de embed, o que evita links arbitrários e XSS.

| Tabela | Campos principais |
|---|---|
| `courses` | `company_id`, `created_by` (nullOnDelete), `title`, `description`, `status`, `material_title` (nullable), `material_drive_id` (nullable), `material_allow_download` (boolean, padrão `false`), timestamps, softDeletes |
| `course_modules` | `course_id`, `company_id` (denormalizado para o escopo), `title`, `position`, `slides_id`, `youtube_id` (nullable), timestamps |
| `course_user` (pivot de acesso) | `course_id`, `user_id`, `granted_by`, `granted_at`; único em (`course_id`, `user_id`) |
| `module_progress` (fase posterior) | `user_id`, `course_module_id`, `completed_at`, `last_accessed_at` |

**Índices sugeridos:** (`company_id`, `status`) em `courses`; (`course_id`, `position`) em `course_modules`.

**Observação:** como há um único PDF por curso, ele fica em colunas da própria tabela `courses`. Se no futuro houver mais de um material, cria-se uma tabela própria sem grande retrabalho.

## 5. Pontos críticos e recomendações

### Google Slides: "ver somente dentro da plataforma"
- O embed por iframe (`/embed`) permite avançar os slides com os controles nativos do Google. Os iframes usam `sandbox` sem permissões para pop-ups ou navegação da página principal.
- O arquivo precisa estar como "qualquer pessoa com o link" ou publicado na web. Como o conteúdo é público, quem obtiver o ID ainda consegue abrir o arquivo diretamente no Google. A sandbox impede os controles incorporados de tirarem o aluno da página, mas não transforma o arquivo público em privado.
- O MVP mantém o aluno na plataforma durante a navegação normal. Se o conteúdo precisar ser realmente privado, evoluir para a Fase 6.

### YouTube
- Usar `youtube-nocookie.com/embed/ID`, `referrerpolicy="strict-origin-when-cross-origin"` e iframe sandbox sem pop-ups ou navegação da página principal.
- Orientar o uso de vídeos **não listados**. Isso não equivale a privado: quem tiver o link consegue assistir.

### PDF e "permitir download"
- O checkbox controla se a plataforma exibe o botão de baixar. O botão usa uma rota autenticada da própria aplicação, que verifica a autorização, busca o PDF público e o transmite como download de mesma origem; o aluno não é redirecionado ao Drive.
- O iframe de prévia usa sandbox sem permissões de download, pop-up ou navegação da página principal.
- O Drive tem configuração própria ("leitores podem baixar, imprimir e copiar"). Se estiver ativa, o usuário ainda consegue baixar.
- Como o arquivo é público, o proxy da plataforma controla o botão na interface, mas não impede alguém que descubra o ID de acessar o arquivo diretamente no Drive. Para proteção efetiva, usar arquivos privados e a Drive API (Fase 6).

### Validação dos links
- Aceitar somente: `docs.google.com/presentation`, `drive.google.com/file` e `youtu.be` / `youtube.com`. Todo o resto é rejeitado.
- Centralizar o tratamento em um serviço único (extrai o ID, valida e reconstrói a URL de embed), com testes.
- Mostrar prévia no formulário antes de salvar e um checklist de compartilhamento.
- Configurar CSP com `frame-src` limitado a `docs.google.com`, `drive.google.com` e `www.youtube-nocookie.com`.

## 6. Controle de acesso e isolamento por empresa

- **Global Scope por `company_id`** (trait reutilizável) em cursos e módulos, conforme já previsto no `instruction.txt` (item 6.4). O admin ignora o filtro.
- **`CoursePolicy`:**
  - Instrutor: gerencia cursos da própria empresa.
  - Usuário: visualiza apenas se o curso estiver publicado, for da sua empresa **e** houver registro em `course_user`.
  - Admin: acesso total.
- **Regra de validação** ao liberar acesso: o usuário deve ter o mesmo `company_id` do curso e `role = user`. Nunca aceitar `company_id` ou `role` vindos do formulário.
- Acessar módulos e material sempre via curso autorizado (rotas aninhadas + Policy em cada rota), para evitar acesso por ID em URL (IDOR).
- Atenção ao Global Scope em seeders, comandos Artisan e jobs, que não têm usuário autenticado.

## 7. Plano de ação por fases

| Fase | Entrega | Detalhes |
|---|---|---|
| **0** | Decisões | Responder as perguntas da seção 8 |
| **1** | Base de dados e domínio | Migrations, models e relacionamentos, trait de escopo por empresa, enum de status, Policies, testes Pest de isolamento entre empresas |
| **2** | Painel do instrutor | Rotas `instructor/courses` com componentes Volt; listagem, criação e edição de curso; repeater de módulos com reordenação; bloco do material complementar; serviço de validação de links |
| **3** | Liberação de usuários | Seleção com busca (só `user` da mesma empresa), "selecionar todos", remover acesso; opcional: modo "todos da empresa" |
| **4** | Área do aluno | "Meus cursos" no `user.dashboard`; página do curso com lista de módulos; visualizador (slides + vídeo); material complementar ao final, com botão de baixar condicionado ao checkbox |
| **5** | Qualidade | Testes de Policy, acesso cruzado entre empresas, parser de links, rascunho/publicado; Pint; revisão da CSP |
| **6** | Endurecimento (opcional) | Conta de serviço do Google: arquivos privados, renderização e entrega controladas pela plataforma, bloqueio real de download; progresso do vídeo via YouTube IFrame API |

**MVP recomendado:** Fases 1 a 4.

## 8. Decisões confirmadas

1. **Material complementar:** disponível desde o início do curso.
2. **Conteúdo do módulo:** Slides obrigatório; vídeo do YouTube opcional.
3. **Edição:** cada instrutor edita somente os cursos que criou.
4. **Liberação:** somente usuários (`role = user`) da mesma empresa.
5. **Ordem dos módulos:** todos disponíveis; a ordem organiza a navegação, sem bloqueio sequencial.
6. **Progresso:** fora do MVP, para uma fase posterior.
7. **Proteção externa:** MVP com embeds e avisos; a Fase 6 continua opcional.

## 9. Riscos resumidos

| Risco | Mitigação |
|---|---|
| Conteúdo público pode ser aberto diretamente no provedor caso o ID seja obtido | Sandboxing mantém a navegação normal na plataforma; Fase 6 para arquivos realmente privados |
| Download do PDF público possível diretamente pelo Drive, fora do botão da plataforma | O botão da aplicação respeita a autorização; Fase 6 para impedir acesso direto ao provedor |
| Vazamento de conteúdo entre empresas | Global Scope + Policies + testes de acesso cruzado |
| Links maliciosos | Armazenar só IDs; whitelist de domínios; CSP |
| Acesso direto por ID na URL | Rotas aninhadas e Policy em toda rota |

## 10. Próximo passo

O MVP das **Fases 1 a 4** está implementado: domínio e isolamento por empresa, painel do instrutor, liberação individual de alunos e área de cursos do aluno. Os testes da Fase 5 cobrem parser, autorização e isolamento entre empresas; o build frontend e o Pint também foram executados. A Fase 6 permanece opcional para quando houver requisito de proteção forte de conteúdo externo.
