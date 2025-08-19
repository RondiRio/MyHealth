# verificador_medico.py
import requests
from bs4 import BeautifulSoup
import json
import sys
import re

def buscar_google(query: str) -> list:
    """
    Simula uma busca no Google e retorna os primeiros URLs.
    NOTA: Em um ambiente real, o ideal é usar uma API de busca paga (Google/Bing)
    para evitar bloqueios. Esta função usa DuckDuckGo como alternativa gratuita.
    """
    try:
        # Usamos o DuckDuckGo (versão HTML) que é mais amigável para scraping
        url = f"https://html.duckduckgo.com/html/?q={query}"
        headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'}
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        
        soup = BeautifulSoup(response.text, 'html.parser')
        links = [a['href'] for a in soup.select('a.result__a')]
        return links[:5] # Retorna os 5 primeiros resultados
    except requests.RequestException as e:
        # Em caso de erro na busca, retorna uma lista vazia silenciosamente
        return []

def analisar_conteudo(url: str, nome_medico: str, crm: str) -> dict:
    """
    Analisa o conteúdo de uma página em busca de dados do médico.
    """
    pontos = 0
    contatos_encontrados = []
    
    try:
        headers = {'User-Agent': 'Mozilla/5.0'}
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        texto_pagina = response.text.lower()

        # Verifica se o nome (ou partes dele) e o CRM estão presentes na página
        partes_nome = nome_medico.lower().split()
        if len(partes_nome) > 1 and partes_nome[0] in texto_pagina and partes_nome[-1] in texto_pagina:
            pontos += 2 # Pontuação maior se encontrar o primeiro e último nome
        
        if crm in texto_pagina:
            pontos += 3 # O CRM é um identificador mais forte

        # Tenta extrair e-mails usando uma expressão regular mais robusta
        emails = re.findall(r'[\w\.\-]+@[\w\.\-]+\.\w+', texto_pagina)
        for email in emails:
            # Filtra e-mails genéricos para focar em e-mails profissionais
            if 'suporte' not in email and 'contato' not in email and 'example' not in email:
                contatos_encontrados.append(email)

        return {"pontos": pontos, "contatos": list(set(contatos_encontrados))}
    except (requests.RequestException, ValueError):
        return {"pontos": 0, "contatos": []}


def verificar_pegada_digital(nome_completo: str, crm: str, uf: str) -> dict:
    """
    Função principal que analisa a presença online de um médico e retorna um score de confiança.
    """
    score_confianca = 0
    contatos_publicos = []
    
    # Monta a query de busca, colocando o nome entre aspas para uma busca mais precisa
    query = f'"{nome_completo}" CRM {crm} {uf}'
    
    urls = buscar_google(query)
    
    if not urls:
        return {"score": 0, "contatos": [], "motivo": "Nenhuma presença online encontrada para os dados fornecidos."}

    for url in urls:
        resultado_analise = analisar_conteudo(url, nome_completo, crm)
        score_confianca += resultado_analise["pontos"]
        contatos_publicos.extend(resultado_analise["contatos"])

    # Normaliza o score (exemplo simples de pontuação)
    score_final = min(score_confianca * 10, 100) # Multiplica por 10, com teto de 100

    return {
        "score": score_final,
        "contatos": list(set(contatos_publicos)),
        "motivo": f"Análise concluída em {len(urls)} fontes. Score de confiança: {score_final}%"
    }

if __name__ == "__main__":
    # Esta parte é executada quando o script é chamado pelo PHP
    if len(sys.argv) != 4:
        # Retorna um erro em JSON se os argumentos não forem passados corretamente
        print(json.dumps({"error": "Uso: python verificador_medico.py \"Nome Completo\" CRM UF"}))
        sys.exit(1)
        
    nome = sys.argv[1]
    crm = sys.argv[2]
    uf = sys.argv[3]
    
    resultado = verificar_pegada_digital(nome, crm, uf)
    
    # Imprime o resultado como JSON para que o PHP possa lê-lo
    print(json.dumps(resultado))

