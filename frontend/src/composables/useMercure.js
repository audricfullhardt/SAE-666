// Abonnement SSE à un topic Mercure.
// Le backend publie des messages de la forme { event, data } sur le topic "game/{code}".
const MERCURE_URL = 'http://localhost:3000/.well-known/mercure'

export function useMercure(topic, onMessage) {
  let es = null

  function subscribe() {
    if (es) return
    const url = new URL(MERCURE_URL)
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
