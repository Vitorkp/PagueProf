# 📚 PagueProf

> **Plataforma para auxiliar professores autônomos no gerenciamento de alunos, aulas e pagamentos.**

<p align="center">

**Organize seus alunos. Controle seus pagamentos. Simplifique sua rotina.**

</p>

---

## 📌 Sobre o projeto

O **PagueProf** é uma proposta de plataforma desenvolvida para auxiliar **professores autônomos** que trabalham com aulas particulares ou pequenos grupos.

A solução é voltada para profissionais de diferentes áreas, como:

* 🎵 Professores de música
* 🎨 Professores de desenho e pintura
* 📖 Professores particulares
*  professores que trabalham com aulas individuais


O projeto surgiu a partir de uma dificuldade comum enfrentada por esses profissionais: **o controle manual de alunos, aulas e pagamentos**.

Atualmente, muitos professores utilizam planilhas, anotações ou conversas pelo WhatsApp para acompanhar pagamentos, aulas contratadas e cobranças. Esse processo pode ser trabalhoso, gerar esquecimentos e dificultar a visualização da situação financeira.

O **PagueProf** busca centralizar essas informações em um único ambiente, tornando o gerenciamento **mais simples, rápido e organizado**.

---

## 🎯 Objetivo

O principal objetivo do PagueProf é **facilitar a gestão financeira e administrativa de professores autônomos**, permitindo acompanhar seus alunos, pacotes de aulas e pagamentos de maneira rápida e intuitiva.

A plataforma também busca facilitar o contato com alunos ou responsáveis em situações de **pagamento pendente ou atrasado**.

### Objetivos específicos

* 👨‍🏫 Gerenciar alunos;
* 📚 Controlar pacotes de aulas;
* 💰 Acompanhar pagamentos;
* 📊 Visualizar informações através de um dashboard;
* 🔔 Facilitar cobranças e lembretes;
* 📱 Centralizar informações dos alunos;
* 🗂️ Manter um histórico organizado.

---

## 💡 Problema

Professores autônomos frequentemente precisam controlar manualmente informações como:

* Quais alunos possuem aulas;
* Qual modalidade de aula cada aluno realiza;
* Quantas aulas foram contratadas;
* Quais pagamentos já foram realizados;
* Quais pagamentos estão pendentes;
* Quais pagamentos estão atrasados;
* Quando cada pagamento deve ser realizado;
* Quem precisa receber uma cobrança.

Além disso, realizar cobranças individualmente por mensagens pode ser uma tarefa **repetitiva, demorada e pouco prática**.

### ❓ Como o PagueProf ajuda?

A proposta é substituir controles espalhados em diferentes ferramentas por **uma plataforma/app centralizado**, permitindo que o professor visualize rapidamente a situação de seus alunos e pagamentos.

---

# 🚀 Funcionalidades propostas

##  Gerenciamento de alunos

O professor poderá cadastrar e consultar seus alunos, armazenando as informações necessárias para o acompanhamento das aulas e pagamentos.

Entre as informações previstas:

* Nome;
* Modalidade da aula;
* Contato;
* Pacote contratado;
* Quantidade de aulas;
* Status do pagamento;
* Histórico.

---

## 📚 Pacotes de aulas

O professor poderá cadastrar diferentes pacotes de aulas de acordo com sua forma de trabalho.

### Exemplos:

| Pacote           |     Quantidade de aulas |
| ---------------- | ----------------------: |
| 📘 Básico        |                 4 aulas |
| 📗 Intermediário |                 8 aulas |
| 📕 Completo      |                12 aulas |
| ⚙️ Personalizado | Definido pelo professor |

Cada pacote poderá possuir informações como:

* Quantidade de aulas;
* Valor;
* Modalidade;
* Período de utilização.

---

## 💰 Controle de pagamentos

O sistema permitirá visualizar a situação financeira dos alunos de maneira simples e visual.

### Exemplo

| Aluno       | Aula    |     Valor | Status      |
| ----------- | ------- | --------: | ----------- |
| João Silva  | Violão  | R$ 400,00 | 🟢 Pago     |
| Maria Souza | Desenho | R$ 250,00 | 🟡 Pendente |
| Vitor   | Piano   | R$ 250,00 | 🔴 Atrasado |

Os diferentes status poderão ser representados visualmente para facilitar a identificação.

### Status previstos

* 🟢 **Pago**
* 🟡 **Pendente**
* 🔴 **Atrasado**

---

## 📊 Dashboard

O dashboard terá como objetivo apresentar um **resumo das principais informações do sistema**.

Entre os indicadores previstos:

* 👥 Total de alunos;
* 🟢 Pagamentos realizados;
* 🟡 Pagamentos pendentes;
* 🔴 Pagamentos atrasados;
* 💰 Valores a receber.

A intenção é permitir que o professor compreenda rapidamente a situação atual sem precisar consultar diferentes lugares.

---

## 📱 Área do aluno

Ao selecionar um aluno, o professor poderá visualizar informações detalhadas, como:

```text
Nome do aluno
├── Modalidade da aula
├── Pacote contratado
├── Quantidade de aulas
├── Aulas realizadas
├── Aulas restantes
├── Próximo pagamento
├── Histórico de pagamentos
└── Informações de contato
```

---

## 🔔 Cobranças e notificações

Uma das principais funcionalidades propostas é facilitar o envio de **lembretes de pagamento**.

Dependendo da implementação final, o professor poderá:

* 📧 Enviar uma notificação por e-mail;
* 💬 Abrir uma conversa pelo WhatsApp;
* 📝 Utilizar uma mensagem previamente preparada;
* ✏️ Editar a mensagem antes de enviá-la.

### Exemplo de mensagem

> Olá, tudo bem? Passando para lembrar que o pagamento referente às aulas está pendente. O valor é de R$ 400,00. Qualquer dúvida, estou à disposição!

---

# 🖥️ Interface

A proposta inicial da interface busca ser:

*  Simples;
*  Limpa;
*  Intuitiva;
*  Rápida;
*  Focada nas informações mais importantes.

A tela inicial deverá apresentar um resumo dos alunos e pagamentos, enquanto a área de alunos permitirá acessar informações individuais.

> ⚠️ **Observação:** As telas apresentadas são apenas protótipos conceituais. A interface e as funcionalidades poderão sofrer alterações durante o desenvolvimento.

---

# 🔄 Fluxo básico do sistema

O funcionamento inicialmente ainda estamos planejando para a plataforma, mas uma das ideias iniciais seria:

```text
                         👨‍🏫 Professor
                              │
                              ▼
                       🔐 Login / Cadastro
                              │
                              ▼
                         📊 Dashboard
                              │
            ┌─────────────────┼─────────────────┐
            ▼                 ▼                 ▼
        👥 Alunos         💰 Pagamentos     ⚙️ Configurações
            │                 │
            ▼                 ├── 🟢 Pagos
    Informações do aluno      ├── 🟡 Pendentes
            │                 └── 🔴 Atrasados
            ├── 📚 Pacote
            ├── 📅 Aulas
            ├── 💳 Pagamentos
            └── 📱 Contato
```

---

# 🗃️ Estrutura de dados

Como a definição do sistema ainda está em andamento, algumas entidades previstas são:

```text
                    👨‍🏫 Professor
                         │
                         ▼
                      👤 Aluno
                         │
             ┌───────────┼───────────┐
             ▼           ▼           ▼
        📚 Pacote     📅 Aula    💰 Pagamento
                         │
                         ▼
                   🔔 Notificação
```

A estrutura definitiva do banco de dados será definida durante as etapas do projeto junto com a equipe.

---

# 🛠️ Tecnologias

As tecnologias ainda estão em processo de definição, mas as do frontend ja são fixas.

| Área                     | Tecnologia   |
| ------------------------ | ------------ |
| 🎨 Frontend              | Html, Css e javascript    |
| ⚙️ Backend               | A definir    |
| 🗄️ Banco de dados       | A definir    |
| 🔀 Versionamento         | Git / GitHub |
| 🎨 Design / Prototipação | A definir    |

> 🔧 As tecnologias poderão ser alteradas conforme as necessidades do projeto e as decisões da equipe.

---

📋 Status do projeto
🟡 Em desenvolvimento — Implementação inicial

O PagueProf concluiu sua primeira Sprint, na qual foram definidas as tecnologias, os papéis da equipe e desenvolvido o protótipo visual inicial.

A Sprint 2 dará início à implementação do sistema, com foco na estrutura do Front-end, modelagem do banco de dados, configuração do PHP e criação da primeira tela de autenticação do usuário.

✅ Concluído na Sprint 1
 Definição das tecnologias;
 Definição dos papéis da equipe;
 Criação do protótipo visual inicial;
 Definição inicial da proposta do sistema.
 
🔄 Em desenvolvimento
 Estrutura do Front-end;
 Tela de autenticação;
 Modelagem do banco de dados;
 Configuração do PHP;
 Conexão PHP com MySQL;
 Testes;
---

# 👥 Equipe

## Equipe PagueProf

Projeto desenvolvido por alunos do curso de **Análise e Desenvolvimento de Sistemas (ADS)**.

| Integrante      | Função    |
| --------------- | --------- |
| 👨‍💻 Vitor Kannã, | FrontEnd |
| 👨‍💻 A definir | BackEnd     |
| 👨‍💻 A definir | A definir   |

> Os integrantes e suas respectivas funções poderão ser adicionados ou atualizados posteriormente.

---

## 📌 Resumo

> **PagueProf** é uma proposta de plataforma criada para simplificar a rotina de professores autônomos, centralizando **alunos, aulas, pacotes e pagamentos** em um único lugar.

**Menos planilhas. Menos anotações. Menos preocupação e mais organização para o professor autonomo**

---

<p align="center">
   <strong>PagueProf</strong><br>
  <i>Organizando a rotina de quem ensina.</i>
</p>
