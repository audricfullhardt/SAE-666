// Abonnement SSE à un topic Mercure.
// Le backend publie des messages de la forme { event, data } sur le topic "game/{code}".
// On utilise une URL relative (même origine que la page) : le hub est atteint via un proxy
// — Vite en dev, Nginx en prod. Aucune IP ni localhost en dur, donc ça marche aussi bien
// sur ce PC, sur un téléphone du même réseau (http://IP:port) qu'en prod (https://domaine).
export function useMercure(topic, onMessage) {
  let es = null

  function subscribe() {
    if (es) return
    const url = new URL('/.well-known/mercure', window.location.origin)
    url.searchParams.append('topic', topic)
    es = new EventSource(url)
    es.onmessage = (e) => {
      try {
        onMessage(JSON.parse(e.data))
      } catch {
        // message non-JSON ignoré
      }
    }
  }

  function unsubscribe() {
    if (es) {
      es.close()
      es = null
    }
  }

  return { subscribe, unsubscribe }
}
