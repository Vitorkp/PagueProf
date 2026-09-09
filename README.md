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
* 🌎 Professores de idiomas
* 💻 Professores de programação
* 🏋️ Instrutores e professores que trabalham com aulas individuais


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

A proposta é substituir controles espalhados em diferentes ferramentas por **uma plataforma centralizada**, permitindo que o professor visualize rapidamente a situação de seus alunos e pagamentos.

---

# 🚀 Funcionalidades propostas

## 👨‍🏫 Gerenciamento de alunos

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
| Ana Costa   | Piano   | R$ 350,00 | 🔴 Atrasado |

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
* 💵 Pagamentos realizados;
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

* ✨ Simples;
* 🧹 Limpa;
* 📱 Intuitiva;
* ⚡ Rápida;
* 📊 Focada nas informações mais importantes.

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

A estrutura definitiva do banco de dados será definida durante as etapas do projeto.

---

# 🛠️ Tecnologias

As tecnologias ainda estão em processo de definição.

| Área                     | Tecnologia   |
| ------------------------ | ------------ |
| 🎨 Frontend              | A definir    |
| ⚙️ Backend               | A definir    |
| 🗄️ Banco de dados       | A definir    |
| 🔀 Versionamento         | Git / GitHub |
| 🎨 Design / Prototipação | A definir    |

> 🔧 As tecnologias poderão ser alteradas conforme as necessidades do projeto e as decisões da equipe.

---

# 📋 Status do projeto

### 🟡 Em desenvolvimento — Fase de planejamento

O PagueProf encontra-se em sua fase inicial de desenvolvimento.

A proposta, as funcionalidades e a arquitetura ainda serão modificadas e atualizadas.

### ✅ Já definido

* [x] Ideia inicial do sistema
* [x] Identificação do problema
* [x] Definição inicial do público-alvo
* [x] Proposta de solução
* [x] Protótipo conceitual das telas

### 🔄 Em desenvolvimento

* [ ] Levantamento completo de requisitos
* [ ] Definição da arquitetura
* [ ] Modelagem do banco de dados
* [ ] Desenvolvimento do frontend
* [ ] Desenvolvimento do backend
* [ ] Integração entre frontend e backend
* [ ] Testes
* [ ] Documentação final

---

# 🎓 Projeto acadêmico

O **PagueProf** está sendo desenvolvido como parte de um **Projeto de Desenvolvimento do curso de Análise e Desenvolvimento de Sistemas (ADS)**.

O projeto tem como objetivo aplicar, na prática, conceitos relacionados a:

* Engenharia de Software;
* Análise de requisitos;
* Modelagem de sistemas;
* Banco de dados;
* Desenvolvimento de interfaces;
* Desenvolvimento de aplicações;
* Testes de software;
* Documentação;
* Controle de versão.

---

# 👥 Equipe

## Equipe PagueProf

Projeto desenvolvido por alunos do curso de **Análise e Desenvolvimento de Sistemas (ADS)**.

| Integrante      | Função    |
| --------------- | --------- |
| 👨‍💻 A definir | A definir |
| 👨‍💻 A definir | A definir |
| 👨‍💻 A definir | A definir |

> Os integrantes e suas respectivas funções poderão ser adicionados ou atualizados posteriormente.

---

# 📄 Observações

O **PagueProf** é atualmente uma proposta em desenvolvimento.

Portanto, as funcionalidades descritas neste README representam a **visão atual do projeto** e não necessariamente a versão final do sistema.

Durante o desenvolvimento, novas funcionalidades poderão ser adicionadas, enquanto outras poderão ser modificadas ou removidas de acordo com as necessidades identificadas pela equipe.

---

## 📌 Resumo

> **PagueProf** é uma proposta de plataforma criada para simplificar a rotina de professores autônomos, centralizando **alunos, aulas, pacotes e pagamentos** em um único lugar.

**Menos planilhas. Menos anotações. Menos preocupação.
Mais organização para o professor. 💙**

---

<p align="center">
  📚 <strong>PagueProf</strong><br>
  <i>Organizando a rotina de quem ensina.</i>
</p>
