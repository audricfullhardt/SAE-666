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
        return
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
