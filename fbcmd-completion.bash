# bash completion for fbcmd

fbcmd_commands=$(fbcmd BC_COMMANDS)
fbcmd_preferences=$(fbcmd BC_PREFERENCES)

_fbcmd() {

  local cur prev
  cur="${COMP_WORDS[COMP_CWORD]}"
  prev="${COMP_WORDS[COMP_CWORD-1]}"

  if [[ "$cur" == -* ]] ; then
    COMPREPLY=( $(compgen -W "${fbcmd_preferences}" -- $cur) )
  else
    COMPREPLY=( $(compgen -W "${fbcmd_commands}" -- $cur) )
  fi

  return 0

}

complete -F _fbcmd fbcmd
